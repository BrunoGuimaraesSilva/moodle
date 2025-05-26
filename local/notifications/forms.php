<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class local_notifications_form extends moodleform {
    function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('header', 'general', get_string('notificationdetails', 'local_notifications'));

        $mform->addElement('text', 'title', get_string('subject', 'local_notifications'));
        $mform->addHelpButton('title', 'subject_help', 'local_notifications');
        $mform->addRule('title', get_string('required', 'local_notifications'), 'required', null, 'client');
        $mform->setType('title', PARAM_TEXT);

        $mform->addElement('html', '<p>' . get_string('variablesinfo', 'local_notifications') . '</p>' .
            '<ul>' .
            '<li><code>%%ALUNO_NOME%%</code> = ' . get_string('studentname', 'local_notifications') . '</li>' .
            '<li><code>%%ALUNO_EMAIL%%</code> = ' . get_string('studentemail', 'local_notifications') . '</li>' .
            '</ul>');

        $mform->addElement('editor', 'message', get_string('message', 'local_notifications'), array('maxfiles' => EDITOR_UNLIMITED_FILES));
        $mform->addRule('message', get_string('required', 'local_notifications'), 'required', null, 'client');
        $mform->setType('message', PARAM_RAW);

        $mform->addElement('date_time_selector', 'senddate', get_string('senddate', 'local_notifications'));
        $mform->addRule('senddate', get_string('required', 'local_notifications'), 'required', null, 'client');

        $mform->addElement('html', '<p class="form-description">' .
            '<a href="' . new moodle_url('/admin/settings.php', ['section' => 'local_notificationsettings']) . '">' .
            get_string('managenotifications', 'local_notifications')  . '</a></p>');

        $this->add_action_buttons(true, get_string('sendnotification', 'local_notifications'));
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['senddate'] <= time()) {
            $errors['senddate'] = get_string('senddatefuture', 'local_notifications');
        }

        return $errors;
    }
}