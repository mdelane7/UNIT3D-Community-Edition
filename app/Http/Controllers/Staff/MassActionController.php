<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMassPM;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\MassActionControllerTest
 */
class MassActionController extends Controller
{
    /**
     * @var int
     */
    private const SENDER_ID = 1;

    /**
     * Mass PM Form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return \view('Staff.masspm.index');
    }

    /**
     * Send The Mass PM.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $banned_group = \cache()->rememberForever('banned_group', fn () => Group::where('slug', '=', 'banned')->pluck('id'));
        $validating_group = \cache()->rememberForever('validating_group', fn () => Group::where('slug', '=', 'validating')->pluck('id'));
        $disabled_group = \cache()->rememberForever('disabled_group', fn () => Group::where('slug', '=', 'disabled')->pluck('id'));
        $pruned_group = \cache()->rememberForever('pruned_group', fn () => Group::where('slug', '=', 'pruned')->pluck('id'));
        $users = User::whereNotIn('group_id', [$validating_group[0], $banned_group[0], $disabled_group[0], $pruned_group[0]])->pluck('id');

        $subject = $request->input('subject');
        $message = $request->input('message');

        $v = \validator($request->all(), [
            'subject' => 'required|min:5',
            'message' => 'required|min:5',
        ]);

        if ($v->fails()) {
            return \redirect()->route('staff.mass-pm.create')
                ->withErrors($v->errors());
        }

        foreach ($users as $user_id) {
            ProcessMassPM::dispatch(self::SENDER_ID, $user_id, $subject, $message);
        }

        return \redirect()->route('staff.mass-pm.create')
            ->withSuccess('MassPM Sent');
    }

    /**
     * Mass Validate Unvalidated Users.
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $validating_group = \cache()->rememberForever('validating_group', fn () => Group::where('slug', '=', 'validating')->pluck('id'));
        $member_group = \cache()->rememberForever('member_group', fn () => Group::where('slug', '=', 'user')->pluck('id'));
        foreach (User::where('group_id', '=', $validating_group[0])->get() as $user) {
            $user->group_id = $member_group[0];
            $user->active = 1;
            $user->can_upload = 1;
            $user->can_download = 1;
            $user->can_request = 1;
            $user->can_comment = 1;
            $user->can_invite = 1;
            $user->save();
        }

        return \redirect()->route('staff.dashboard.index')
            ->withSuccess('Unvalidated Accounts Are Now Validated');
    }
}
