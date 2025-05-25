<?php

require_once('../../config.php');

$id = required_param('id', PARAM_INT);

$notification = $DB->get_record('local_notifications', ['id' => $id], '*', MUST_EXIST);
$courseid = $notification->courseid;
$context = context_course::instance($courseid);

require_login($courseid);
require_capability('local/notifications:manage', $context);

require_sesskey();
$DB->delete_records('local_notifications', ['id' => $id]);

redirect(new moodle_url('/local/notifications/index.php', ['id' => $courseid]));
