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
 * Plugin information.
 *
 * @package   datafield_emailnotification
 * @copyright 2023 David Herney @ BambuCo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class data_field_emailnotification extends data_field_base {

    var $type = 'emailnotification';

    /**
     * Print the relevant form element in the ADD template for this field.
     *
     * @param int $recordid
     * @param object $formdata
     * @return string
     */
    function display_add_field($recordid = 0, $formdata = null) {
        $str = get_string("emailnotificationtext", "datafield_emailnotification") .
                ' <input type="hidden" name="field_' . $this->field->id . '" id="field_' . $this->field->id . '" value="" />';
        return $str;
    }

    /**
     * Print the relevant form element to define the attributes for this field viewable by teachers only.
     *
     * @param int $recordid
     * @param object $formdata
     * @return string
     */
    function display_edit_field($recordid = 0, $formdata = null) {
        parent::display_edit_field();
    }

    /**
     * Display the content of the field in browse mode
     *
     * @param int $recordid
     * @param object $template
     * @return string
     */
    function display_browse_field($recordid, $template) {
        return get_string("emailnotdisplaybrowse", "datafield_emailnotification");
    }

    /**
     * Update the content of one data field in the data_content table
     *
     * @param int $recordid
     * @param string $value
     * @param string $name
     * @return boolean
     */
    function update_content($recordid, $value, $name = '') {
        global $data, $CFG, $DB;

        // Send the email.
        if (!empty($this->field->param1)) {
        $trim_users = trim($this->field->param1, ',');
        $array_users_id = explode(',', $trim_users);
            $users = $DB->get_records_list('user', 'id', $array_users_id);

            if (is_array($users)) {
                $url = $CFG->wwwroot . '/mod/data/view.php?d=' . $data->id . '&rid=' . $recordid;
                $subject = get_string('emailnotregisterbd', 'datafield_emailnotification') . $data->name;
                $messagehtml = str_replace('[link]', '<a href="' . $url . '">' . $url . '</a>', $this->field->param2);
                $messagetext = strip_tags($messagehtml);

                foreach ($users as $user) {
                    email_to_user($user, null, $subject, $messagetext, $messagehtml);
                }
            }
        }

        return true;
    }

    /**
     * Delete all content associated with the field
     *
     * @param int $recordid
     * @return boolean
     */
    function delete_content($recordid = 0) {
        return true;
    }

    /**
     * Check if a field from an add form is empty
     *
     * @param string $value
     * @param string $name
     * @return boolean
     */
    function notemptyfield($value, $name) {
        return true;
    }

    /**
     * Per default, it is assumed that fields support text exporting. Override this (return false) on fields not supporting text exporting.
     *
     * @return boolean
     */
    function text_export_supported() {
        return false;
    }

    /**
     * Content when field is used to search.
     *
     * @param string $value
     * @return string
     */
    function display_search_field($value = '') {
        return '-----------------------------';
    }

    /**
     * Parse search field.
     *
     * @return string
     */
    function parse_search_field() {
        return '';
    }

    /**
     * Generate SQL for search.
     *
     * @param string $tablealias
     * @param string $value
     * @return string
     */
    function generate_sql($tablealias, $value) {
        return '';
    }

    /**
     * Prints the respective type icon
     *
     * @global object
     * @return string
     */
    function image() {
        global $OUTPUT;

        $params = ['d' => $this->data->id, 'fid' => $this->field->id, 'mode' => 'display', 'sesskey' => sesskey()];
        $link = new moodle_url('/mod/data/field.php', $params);
        $str = '<a href="' . $link->out() . '">';
        $str .= $OUTPUT->pix_icon('field/' . $this->type, $this->type, 'datafield_' . $this->type);
        $str .= '</a>';
        return $str;
    }

}
