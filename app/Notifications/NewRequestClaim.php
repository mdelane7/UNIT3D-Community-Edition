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

namespace App\Notifications;

use App\Models\TorrentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewRequestClaim extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * NewRequestClaim Constructor.
     *
     * @param string                     $type
     * @param string                     $sender
     * @param \App\Models\TorrentRequest $tr
     */
    public function __construct(public string $type, public string $sender, public TorrentRequest $tr)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        $appurl = \config('app.url');

        return [
            'title' => $this->sender.' Has Claimed One Of Your Requested Torrents',
            'body'  => $this->sender.' has claimed your Requested Torrent '.$this->tr->name,
            'url'   => \sprintf('/requests/%s', $this->tr->id),
        ];
    }
}
