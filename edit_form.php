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
 * Form for editing QRCode block instances.
 *
 * @package   block_qrcode
 * @copyright 2016 Chi-Wen Fann (http://www.kipt.com.tw)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_qrcode_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG;

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_qrcode'));
        $mform->setType('config_title', PARAM_TEXT);
        $mform->setDefault('config_title', get_string('blocktitle', 'block_qrcode'));

        //設定QRCode的顏色(如:000000)
        $mform->addElement('text', 'config_qrcodecolor', get_string('config_qrcode_color', 'block_qrcode'));        
        $mform->setType('config_qrcodecolor', PARAM_TEXT);        
        $mform->setDefault('config_qrcodecolor', '000000');

        //設定QRCode的尺寸大小(pixels)
        $attributes = array('size' => '3', 'maxlength' => '3');
        $mform->addElement('text', 'config_qrcodesize', get_string('config_qrcode_size', 'block_qrcode'), $attributes);
        $mform->setType('config_qrcodesize', PARAM_TEXT);        
        $mform->setDefault('config_qrcodesize', 250);

        $mform->addRule('config_text', null, 'required', null, 'client');
        $mform->setType('config_text', PARAM_RAW); // XSS is prevented when printing the block contents and serving files
    }

    function set_data($defaults) {
        if (!empty($this->block->config) && is_object($this->block->config)) {
            $text = $this->block->config->text;
            $draftid_editor = file_get_submitted_draft_itemid('config_text');
            if (empty($text)) {
                $currenttext = '';
            } else {
                $currenttext = $text;
            }
            $defaults->config_text['text'] = file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_qrcode', 'content', 0, array('subdirs'=>true), $currenttext);
            $defaults->config_text['itemid'] = $draftid_editor;
            $defaults->config_text['qrcodecolor'] = $this->block->config->qrcodecolor;
            $defaults->config_text['qrcodesize'] = $this->block->config->qrcodesize;
        } else {
            $text = '';
        }

        if (!$this->block->user_can_edit() && !empty($this->block->config->title)) {
            // If a title has been set but the user cannot edit it format it nicely
            $title = $this->block->config->title;
            $defaults->config_title = format_string($title, true, $this->page->context);
            // Remove the title from the config so that parent::set_data doesn't set it.
            unset($this->block->config->title);
        }

        // have to delete text here, otherwise parent::set_data will empty content
        // of editor
        unset($this->block->config->text);
        parent::set_data($defaults);

        // restore $text
        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }

        $this->block->config->text = $text;
        if (isset($title)) {
            // Reset the preserved title
            $this->block->config->title = $title;
        }
    }
}
