<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    local_cleanupcoursesnavigation
 * @copyright  2018 Tobias Reischmann, Nina Herrmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Adds a navigation node to the menu in case the logged in user has a course with tool/cleanupcourses:managecourses
 * capabilities
 * @param $navigation
 * @throws coding_exception
 * @throws moodle_exception
 */
function local_cleanupcoursesnavigation_extend_navigation($navigation) {
    $courses = get_managedcourses();
    // In case a user has no courses it is not shown in the navigation block.
    if ($courses === 'yes') {
        $url = new moodle_url('/admin/tool/cleanupcourses/view.php');
        $subsnode = navigation_node::create(get_string('managecourses', 'local_cleanupcoursesnavigation'), $url,
            navigation_node::TYPE_SETTING, null, 'cleanupcourses', new pix_icon('i/settings', ''));

        if (!empty($navigation)) {
            // Show in flat navigation themes.
            $subsnode->showinflatnavigation = true;
            $navigation->add_node($subsnode);
        }
    }
}

/**
 * Looks whether managedcourses are cached and returns an array of managed courses.
 * @return array|bool
 */
function get_managedcourses() {
    global $USER;
    $cache = cache::make('local_cleanupcoursesnavigation', 'coursesmanaged');
    $cachedcourses = $cache->get(0);

    if ($cachedcourses === false) {
        $cachedcourses = get_user_capability_course('tool/cleanupcourses:managecourses', $USER->id, false);
        // No course with capabilities.
        if ($cachedcourses === false) {
            $cache->set(0, 'no');
        } else {
            $cache->set(0, 'yes');
        }
        $cachedcourses = $cache->get(0);

    }
    return $cachedcourses;
}