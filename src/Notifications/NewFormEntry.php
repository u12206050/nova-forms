<?php

namespace Day4\NovaForms\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\URL;
use Day4\NovaForms\Models\FormEntry;

class NewFormEntry extends Notification implements ShouldQueue
{
    use Queueable;
    public $entry;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FormEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (empty($this->entry)) return null;

        app()->setLocale($this->entry->locale);

        $mail = (new MailMessage)
            ->subject('New FormEntry: ' . $this->entry->form->title)
            ->greeting('Info:');

        $mail->line($this->ArrayToHTMLTable($this->entry->fields));

        if (isset($this->entry->terms)) {
            $mail->line('T&Cs');
            $mail->line($this->ArrayToHTMLTable($this->entry->terms));
        }

        $tracker = URL::signedRoute('track_form_entry', ['entryId' => $this->entry->id]);
        $mail->line(new HtmlString("<img src='$tracker' alt='tracker' width='1' height='1' />"));

        return $mail;
    }

    private function ArrayToHTMLTable($array) {
        $html = '<table><tbody>';
        foreach($array as $key => $value) {
            if (is_bool($value)) $value = $value ? __('site.yes') : __('site.no');
            if (!empty($value))
                $html .= '<tr><td valign="top"><b>' . $key . ':</b></td><td valign="bottom"><pre style="margin:0;padding:0 0 6px 6px;">' . trim($value) . '</pre></td></tr>';
        }
        $html .= '</tbody></table><hr/>';
        return new HtmlString($html);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
