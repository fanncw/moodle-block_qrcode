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
 * Form for QRCode block instances.
 *
 * @package   block_qrcode
 * @copyright 2016 Chi-Wen Fann (http://www.kipt.com.tw)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_qrcode extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_qrcode');
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? format_string($this->config->title) : format_string(get_string('blocktitle', 'block_qrcode'));
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $CFG;
        global $SCRIPT;
        require_once($CFG->libdir . '/filelib.php');

        $filteropt = new stdClass;
        $filteropt->overflowdiv = true;
        if ($this->content_is_trusted()) {
            // fancy html allowed only on course, category and system blocks.
            $filteropt->noclean = true;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $format = FORMAT_HTML;
        $qrcode_Size = '250';
        $qrcode_Color = '000000';
        if (isset($this->config->qrcodesize) && isset($this->config->qrcodecolor)) {
            $qrcode_Size = $this->config->qrcodesize;
            $qrcode_Color = $this->config->qrcodecolor;   
        }
        $qrcode_Link = urlencode($this->page->url);
        $qrcodeURL = $CFG->wwwroot . '/blocks/qrcode/qr.php?data=' . $this->page->url . '&size=' . $qrcode_Size . '&color=' . $qrcode_Color;
        $QRCodeJS =<<<EOF
<div id="qrcode" style="margin: 0px auto;"><img class="img-responsive" src="$qrcodeURL" /></div>
EOF;
        $this->content->text = format_text($QRCodeJS, $format, $filteropt);

        unset($filteropt); // memory footprint
        return $this->content;
    }


    /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {
        global $DB;
        $config = clone($data);
        parent::instance_config_save($config, $nolongerused);
    }


    function instance_delete() {
        global $DB;
        return true;
    }


    /**
     * Copy any block-specific data when copying to a new block instance.
     * @param int $fromid the id number of the block instance to copy from
     * @return boolean
     */
    public function instance_copy($fromid) {
        return true;
    }


    function content_is_trusted() {
        global $SCRIPT;

        if (!$context = context::instance_by_id($this->instance->parentcontextid, IGNORE_MISSING)) {
            return false;
        }
        //find out if this block is on the profile page
        if ($context->contextlevel == CONTEXT_USER) {
            if ($SCRIPT === '/my/index.php') {
                // this is exception - page is completely private, nobody else may see content there
                // that is why we allow JS here
                return true;
            } else {
                // no JS on public personal pages, it would be a big security issue
                return false;
            }
        }

        return true;
    }


    /**
     * The block should only be dockable when the title of the block is not empty
     * and when parent allows docking.
     *
     * @return bool
     */
    public function instance_can_be_docked() {
        return (!empty($this->config->title) && parent::instance_can_be_docked());
    }

}
