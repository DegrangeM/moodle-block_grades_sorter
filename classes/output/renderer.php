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
 * Block grades sorter renderer.
 *
 * @package    block_grades_sorter
 * @copyright  2020 Mathieu Degrange
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_grades_sorter\output;
defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use renderable;

class renderer extends plugin_renderer_base {

    /**
     * Render grades sorter form.
     *
     * @param renderable $gradessorterform The grades sorter form.
     * @return string
     */
    public function render_grades_sorter_form(renderable $gradessorterform) {
        return $this->render_from_template('block_grade_sorter/grades_sorter_form', $gradessorterform->export_for_template($this));
    }
}
