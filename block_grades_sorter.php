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
 * Block to sort grade item automatically in the same order than course activities.
 *
 * @package   block_grades_sorter
 * @copyright 2020 Mathieu Degrange
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_grades_sorter extends block_base {

    public function init() {
        $this->title = get_string('grades_sorter', 'block_grades_sorter');
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $COURSE, $DB, $OUTPUT;

        $canmanage = $this->page->user_is_editing($this->instance->id);
        $this->content = new stdClass;

        if ($canmanage) {
            $sortgrade = optional_param('sortgrade', 0, PARAM_ACTION);
            if ($sortgrade && confirm_sesskey()) {
                $sections = $DB->get_records('course_sections', ['course' => $COURSE->id], 'section', 'id, section, sequence');
                $mods = [];
                foreach ($sections as $section) {
                    if ($section->sequence !== '') {
                        $mods = array_merge($mods, explode(',', $section->sequence));
                    }
                }
                $notmodindex = count($mods) + 1; // Grades that are not activity will be put in last.
                $modules = $DB->get_records_menu('modules', [], '', 'id, name');
                $gradeitems = $DB->get_records('grade_items', ['courseid' => $COURSE->id], 'sortorder', 'id, iteminstance, sortorder, itemtype, itemmodule');
                foreach ($gradeitems as $gradeitem) {
                    if ($gradeitem->itemtype === 'mod') {
                        $modid = $DB->get_field('course_modules', 'id', [
                            'instance' => $gradeitem->iteminstance,
                            'module' => array_search($gradeitem->itemmodule, $modules)
                        ]);
                        $sortorder = array_search($modid, $mods) + 1;
                        $DB->set_field('grade_items', 'sortorder', $sortorder, ['id' => $gradeitem->id]);
                    } else {
                        $DB->set_field('grade_items', 'sortorder', $notmodindex, ['id' => $gradeitem->id]);
                        $notmodindex++;
                    }
                }
                $this->content->text = get_string('sortgradessuccess', 'block_grades_sorter');
                $this->content->footer = '';
            } else {
                $output = $this->page->get_renderer('block_search_forums');
                $searchform = new \block_grades_sorter\output\grades_sorter_form($this->page->course->id);
                $this->content->text = $output->render($searchform);
                $this->content->footer = get_string('footer', 'block_grades_sorter');
            }
        } else {
            $this->content->text   = '';
            $this->content->footer   = '';
        }
        return $this->content;
    }
    public function applicable_formats() {
        return array('course-view' => true);
    }
}
