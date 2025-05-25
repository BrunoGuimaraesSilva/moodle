<?php

namespace local_notifications\task;

defined('MOODLE_INTERNAL') || die();

class send_notifications extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('task_send_notifications', 'local_notifications');
    }

    public function execute() {
        global $DB;

        $now = time();
        $notifications = $DB->get_records_select('local_notifications', 'senddate <= ? AND sent = 0', [$now]);

        foreach ($notifications as $notification) {
            // Example notification: email or Moodle message
            $course = $DB->get_record('course', ['id' => $notification->courseid], '*', MUST_EXIST);

            $context = \context_course::instance($course->id);
            $users = get_enrolled_users($context);

            foreach ($users as $user) {
                $eventdata = new \core\message\message();
                $eventdata->component = 'local_notifications';
                $eventdata->name = 'notification';
                $eventdata->userfrom = \core_user::get_noreply_user();
                $eventdata->userto = $user;
                $eventdata->subject = $notification->title;
                $eventdata->fullmessage = $notification->message;
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml = format_text($notification->message, FORMAT_HTML);
                $eventdata->smallmessage = $notification->title;
                $eventdata->notification = 1;

                message_send($eventdata);
            }

            $notification->sent = 1;
            $DB->update_record('local_notifications', $notification);
        }
    }
}
