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

namespace App\Jobs;

use App\Models\PrivateMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMassPM implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * ProcessMassPM Constructor.
     *
     * @param $sender_id
     * @param $receiver_id
     * @param $subject
     * @param $message
     */
    public function __construct(protected $sender_id, protected $receiver_id, protected $subject, protected $message)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $privateMessage = new PrivateMessage();
        $privateMessage->sender_id = $this->sender_id;
        $privateMessage->receiver_id = $this->receiver_id;
        $privateMessage->subject = $this->subject;
        $privateMessage->message = $this->message;
        $privateMessage->save();
    }
}
