<?php

defined('MOODLE_INTERNAL') || die();

function local_notifications_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('local/notifications:view', $context)) {
        $url = new moodle_url('/local/notifications/index.php', ['id' => $course->id]);
        $node = navigation_node::create(
            get_string('notifications', 'local_notifications'),
            $url,
            navigation_node::TYPE_CUSTOM,
            null,
            'local_notifications',
            new pix_icon('i/notifications', '')
        );
        $navigation->add_node($node);
    }
}


function local_notifications_extend_settings_navigation(settings_navigation $settingsnav, context $context) {
    global $PAGE;

    if ($context->contextlevel === CONTEXT_COURSE) {
        $node = $settingsnav->find('courseadmin', navigation_node::TYPE_CONTAINER);
        if ($node) {
            $url = new moodle_url('/local/notifications/index.php', ['courseid' => $PAGE->course->id]);
            $node->add(get_string('pluginname', 'local_notifications'), $url, navigation_node::TYPE_SETTING);
        }
    }
}