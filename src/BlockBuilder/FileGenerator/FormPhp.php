<?php namespace Concrete\Package\BlockBuilder\Src\BlockBuilder\FileGenerator;

use Concrete\Core\File\Service\File as FileService;
use Concrete\Package\BlockBuilder\Src\BlockBuilder\Utility as BlockBuilderUtility;

defined('C5_EXECUTE') or die('Access Denied.');

class FormPhp
{

    public function generate($postDataSummary, $postData) {

        $filename = 'form.php';

        $code = '';
        $code .= '<?php defined(\'C5_EXECUTE\') or die(\'Access Denied.\'); ?>'.PHP_EOL.PHP_EOL;

        $code .= '<div id="form-container-<?php echo $uniqueID; ?>">'.PHP_EOL.PHP_EOL;

        if ( ! empty($postData['basic']) AND ! empty($postData['entries'])) {
            $code .= BlockBuilderUtility::tab(1).'<?php'.PHP_EOL;
            $code .= BlockBuilderUtility::tab(1).'echo Core::make(\'helper/concrete/ui\')->tabs(array('.PHP_EOL;
            $code .= BlockBuilderUtility::tab(2).'array(\'basic-information-\'.$uniqueID, t(\''.addslashes($postData['basicLabel']).'\'), true),'.PHP_EOL;
            $code .= BlockBuilderUtility::tab(2).'array(\'entries-\'.$uniqueID, t(\''.addslashes($postData['entriesLabel']).'\'))'.PHP_EOL;
            $code .= BlockBuilderUtility::tab(1).'));'.PHP_EOL;
            $code .= BlockBuilderUtility::tab(1).'?>'.PHP_EOL.PHP_EOL;
        }

        if ( ! empty($postData['basic'])) {
            $code .= BlockBuilderUtility::tab(1) . '<div class="js-tab-content';
            if ( ! empty($postData['basic']) AND ! empty($postData['entries'])) {
                $code .= ' ccm-tab-content" id="ccm-tab-content-basic-information-<?php echo $uniqueID; ?>';
            }
            $code .= '">' . PHP_EOL . PHP_EOL;
        }

        if ( ! empty($postData['basic'])) {

            $previousFieldTypeHadMultipleFields = false;
            $i = 0;

            foreach ($postData['basic'] as $k => $v) {

                $i++;

                $required = ! empty($v['required']) ? '.\' *\'' : '';

                // Horizontal line (always)
                if ($postData['fieldsDivider']=='always' AND $i!=1) {
                    $code .= BlockBuilderUtility::tab(2).'<hr/>'.PHP_EOL.PHP_EOL;
                }

                // Horizontal line (smart)
                if ($postData['fieldsDivider'] == 'smart' AND $i!=1) {
                    if (
                        !empty($v['linkFromSitemapShowEndingField']) OR
                        !empty($v['linkFromSitemapShowTextField']) OR
                        !empty($v['linkFromSitemapShowTitleField']) OR
                        !empty($v['linkFromFileManagerShowTextField']) OR
                        !empty($v['linkFromFileManagerShowTitleField']) OR
                        !empty($v['externalLinkShowTextField']) OR
                        !empty($v['externalLinkShowTitleField']) OR
                        !empty($v['imageShowAltTextField'])
                    ) {
                        $code .= BlockBuilderUtility::tab(2) . '<hr/>' . PHP_EOL . PHP_EOL;
                        $previousFieldTypeHadMultipleFields = true;
                    } else {
                        if ($previousFieldTypeHadMultipleFields) {
                            $code .= BlockBuilderUtility::tab(2) . '<hr/>' . PHP_EOL . PHP_EOL;
                            $previousFieldTypeHadMultipleFields = false;
                        }
                    }
                }

                // Field types
                if ($v['fieldType'] == 'text_field') {

                    $code .= BlockBuilderUtility::tab(2).'<div class="form-group">'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(3).'<?php echo $form->label($view->field(\''.$v['handle'].'\'), t(\''.addslashes($v['label']).'\')'.$required.'); ?>'.PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(3) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(3).'<?php echo $form->text($view->field(\''.$v['handle'].'\'), $'.$v['handle'].', array(\'maxlength\'=>\'255\')); ?>'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(2).'</div>'.PHP_EOL.PHP_EOL;

                }

                if ($v['fieldType'] == 'textarea') {

                    $height = !empty($v['textareaHeight']) ? ', array(\'style\'=>\'height: '.$v['textareaHeight'].'px\')' : false;

                    $code .= BlockBuilderUtility::tab(2).'<div class="form-group">'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(3).'<?php echo $form->label($view->field(\''.$v['handle'].'\'), t(\''.addslashes($v['label']).'\')'.$required.'); ?>'.PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(3) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(3).'<?php echo $form->textarea($view->field(\''.$v['handle'].'\'), $'.$v['handle'].$height.'); ?>'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(2).'</div>'.PHP_EOL.PHP_EOL;

                }

                if ($v['fieldType'] == 'wysiwyg_editor') {

                    $height = !empty($v['wysiwygEditorHeight']) ? $v['wysiwygEditorHeight'] : false;

                    $code .= BlockBuilderUtility::tab(2).'<div class="form-group js-custom-editor-height-<?php echo $view->field(\''.$v['handle'].'\'); ?>-<?php echo $uniqueID; ?>">'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(3).'<?php echo $form->label($view->field(\''.$v['handle'].'\'), t(\''.addslashes($v['label']).'\')'.$required.'); ?>'.PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(3) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(3).'<?php echo Core::make(\'editor\')->outputBlockEditModeEditor($view->field(\''.$v['handle'].'\'), $'.$v['handle'].'); ?>'.PHP_EOL;
                    if ($height) {
                        $code .= BlockBuilderUtility::tab(3) . '<style>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(4) . '<?php if ($wysiwygEditor==\'ckeditor\'): ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(4) . '.js-custom-editor-height-<?php echo str_replace(array(\'[\', \']\'), array(\'\[\', \'\]\'), $view->field(\'' . $v['handle'] . '\')); ?>-<?php echo $uniqueID; ?> .cke_contents {' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(5) . 'height: '.$height.'px !important;' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(4) . '}' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(4) . '<?php else :?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(4) . '.js-custom-editor-height-<?php echo str_replace(array(\'[\', \']\'), array(\'\[\', \'\]\'), $view->field(\'' . $v['handle'] . '\')); ?>-<?php echo $uniqueID; ?> .redactor-editor {' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(5) . 'min-height: '.$height.'px !important;' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(4) . '}' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(4) . '<?php endif; ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '</style>' . PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(2).'</div>'.PHP_EOL.PHP_EOL;

                }

                if ($v['fieldType'] == 'select_field') {

                    $code .= BlockBuilderUtility::tab(2).'<div class="form-group">'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(3).'<?php echo $form->label($view->field(\''.$v['handle'].'\'), t(\''.addslashes($v['label']).'\')'.$required.'); ?>'.PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(3) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(3).'<?php echo $form->select($view->field(\''.$v['handle'].'\'), $'.$v['handle'].'_options, $'.$v['handle'].'); ?>'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(2).'</div>'.PHP_EOL.PHP_EOL;

                }

                if ($v['fieldType'] == 'link_from_sitemap') {

                    $code .= BlockBuilderUtility::tab(2).'<div class="form-group">'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(3).'<?php echo $form->label($view->field(\''.$v['handle'].'\'), t(\''.addslashes($v['label']).'\')'.$required.'); ?>'.PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(3) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(3).'<?php  echo Core::make(\'helper/form/page_selector\')->selectPage($view->field(\''.$v['handle'].'\'), (!Page::getByID($'.$v['handle'].')->isError() AND !Page::getByID($'.$v['handle'].')->isInTrash()) ? $'.$v['handle'].' : null); ?>'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(2).'</div>'.PHP_EOL.PHP_EOL;

                    if ( ! empty($v['linkFromSitemapShowEndingField'])) {
                        $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '_ending\'), t(\'' . addslashes($postData['urlEndingLabel']) . '\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<p class="small text-muted"><?php echo t(\''.addslashes($postData['urlEndingHelpText']).'\'); ?></p>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->text($view->field(\'' . $v['handle'] . '_ending\'), $' . $v['handle'] . '_ending, array(\'maxlength\'=>\'255\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                    if ( ! empty($v['linkFromSitemapShowTextField'])) {
                        $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '_text\'), t(\'' . addslashes($postData['textLabel']) . '\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->text($view->field(\'' . $v['handle'] . '_text\'), $' . $v['handle'] . '_text, array(\'maxlength\'=>\'255\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                    if ( ! empty($v['linkFromSitemapShowTitleField'])) {
                        $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '_title\'), t(\'' . addslashes($postData['titleLabel']) . '\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->text($view->field(\'' . $v['handle'] . '_title\'), $' . $v['handle'] . '_title, array(\'maxlength\'=>\'255\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                }

                if ($v['fieldType'] == 'link_from_file_manager') {

                    $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '\'), t(\''.addslashes($v['label']).'\')'.$required.'); ?>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(3) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(3) . '<?php echo Core::make(\'helper/concrete/asset_library\')->file(\'' . $v['handle'] . '-\'.$uniqueID, $view->field(\'' . $v['handle'] . '\'), t(\'Choose Image\'), !empty($' . $v['handle'] . ') ? File::getByID($' . $v['handle'] . ') : null); ?>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;

                    if ( ! empty($v['linkFromFileManagerShowTextField'])) {
                        $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '_text\'), t(\'' . addslashes($postData['textLabel']) . '\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->text($view->field(\'' . $v['handle'] . '_text\'), $' . $v['handle'] . '_text, array(\'maxlength\'=>\'255\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                    if ( ! empty($v['linkFromFileManagerShowTitleField'])) {
                        $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '_title\'), t(\'' . addslashes($postData['titleLabel']) . '\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->text($view->field(\'' . $v['handle'] . '_title\'), $' . $v['handle'] . '_title, array(\'maxlength\'=>\'255\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                }

                if ($v['fieldType'] == 'external_link') {

                    $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '\'), t(\''.addslashes($v['label']).'\')'.$required.'); ?>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(3) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->text($view->field(\'' . $v['handle'] . '\'), $' . $v['handle'] . ', array(\'maxlength\'=>\'255\')); ?>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;

                    if ( ! empty($v['externalLinkShowTextField'])) {
                        $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '_text\'), t(\'' . addslashes($postData['textLabel']) . '\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->text($view->field(\'' . $v['handle'] . '_text\'), $' . $v['handle'] . '_text, array(\'maxlength\'=>\'255\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                    if ( ! empty($v['externalLinkShowTitleField'])) {
                        $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '_title\'), t(\'' . addslashes($postData['titleLabel']) . '\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->text($view->field(\'' . $v['handle'] . '_title\'), $' . $v['handle'] . '_title, array(\'maxlength\'=>\'255\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                }

                if ($v['fieldType'] == 'image') {

                    $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '\'), t(\'' . addslashes($v['label']) . '\')'.$required.'); ?>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(3) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(3) . '<?php echo Core::make(\'helper/concrete/asset_library\')->image(\'' . $v['handle'] . '-\'.$uniqueID, $view->field(\'' . $v['handle'] . '\'), t(\'Choose Image\'), !empty($' . $v['handle'] . ') ? File::getByID($' . $v['handle'] . ') : null); ?>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;

                    if ( ! empty($v['imageShowAltTextField'])) {
                        $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '_alt\'), t(\'' . addslashes($postData['altTextLabel']) . '\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->text($view->field(\'' . $v['handle'] . '_alt\'), $' . $v['handle'] . '_alt, array(\'maxlength\'=>\'255\')); ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                }

                if ($v['fieldType'] == 'html_editor') {

                    $height = !empty($v['htmlEditorHeight']) ? $v['htmlEditorHeight'] : 250;

                    $code .= BlockBuilderUtility::tab(2) . '<div class="form-group">' . PHP_EOL . PHP_EOL;

                    $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->label($view->field(\'' . $v['handle'] . '\'), t(\'' . addslashes($v['label']) . '\')'.$required.'); ?>' . PHP_EOL . PHP_EOL;

                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(3) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'. PHP_EOL . PHP_EOL;
                    }

                    $code .= BlockBuilderUtility::tab(3) . '<div id="' . $v['handle'] . '-html-editor-<?php echo $uniqueID; ?>" style="height: '.$height.'px; border: 1px solid #dedede;"><?php echo h($' . $v['handle'] . '); ?></div>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(3) . '<?php echo $form->textarea($view->field(\'' . $v['handle'] . '\'), $' . $v['handle'] . ', array(\'style\'=>\'display: none;\')); ?>' . PHP_EOL . PHP_EOL;

                    $code .= BlockBuilderUtility::tab(3) . '<script>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(4) . '$(function() {' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . 'var htmlEditor = ace.edit(\'' . $v['handle'] . '-html-editor-<?php echo $uniqueID; ?>\');' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . 'htmlEditor.setTheme(\'ace/theme/eclipse\');' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . 'htmlEditor.getSession().setMode(\'ace/mode/html\');' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . 'htmlEditor.getSession().on(\'change\', function() {' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '$(\'#<?php echo $view->field(\'' . $v['handle'] . '\'); ?>\').val(htmlEditor.getValue());' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . '});' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(4) . '});' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(3) . '</script>' . PHP_EOL . PHP_EOL;

                    $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;

                }

                if ($v['fieldType'] == 'date_picker') {

                    $code .= BlockBuilderUtility::tab(2).'<div class="form-group">'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(3).'<?php echo $form->label($view->field(\''.$v['handle'].'\'), t(\''.addslashes($v['label']).'\')'.$required.'); ?>'.PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(3) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(3).'<?php echo Core::make(\'helper/form/date_time\')->date($view->field(\''.$v['handle'].'\'), $'.$v['handle'].'); ?>'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(2).'</div>'.PHP_EOL.PHP_EOL;

                }

            }

        }

        if ( ! empty($postData['basic'])) {
            $code .= BlockBuilderUtility::tab(1).'</div>'.PHP_EOL.PHP_EOL;
        }


        ///////////////////////////////////////////////////////////////////////////

        if ( ! empty($postData['entries'])) {
            $code .= BlockBuilderUtility::tab(1) . '<div class="js-tab-content';
            if ( ! empty($postData['basic']) AND ! empty($postData['entries'])) {
                $code .= ' ccm-tab-content" id="ccm-tab-content-entries-<?php echo $uniqueID; ?>';
            }
            $code .= '">' . PHP_EOL . PHP_EOL;
        }

        if ( ! empty($postData['entries'])) {

            $code .= BlockBuilderUtility::tab(2) . '<div class="form-group entries-actions">' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(3) . '<button type="button" class="btn btn-primary entries-action-button js-add-entry" data-action="prepend"><?php echo t(\''.addslashes($postData['addAtTheTopLabel']).'\'); ?></button>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(3) . '<button type="button" class="btn btn-primary entries-action-button js-add-entry" data-action="append"><?php echo t(\''.addslashes($postData['addAtTheBottomLabel']).'\'); ?></button>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(3) . '<span class="entries-actions-links">' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(4) . '<a href="#" class="entries-action-link js-expand-all"><i class="fa fa-plus-square-o"></i> <?php echo t(\''.addslashes($postData['expandAllLabel']).'\'); ?></a>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(4) . '<a href="#" class="entries-action-link js-collapse-all"><i class="fa fa-minus-square-o"></i> <?php echo t(\''.addslashes($postData['collapseAllLabel']).'\'); ?></a>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(3) . '</span>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;

            $code .= BlockBuilderUtility::tab(2) . '<div class="entries" id="entries-<?php echo $uniqueID; ?>" data-entries="<?php echo h(json_encode($entries)); ?>" data-column-names="<?php echo h(json_encode($entryColumnNames)); ?>"></div>' . PHP_EOL . PHP_EOL;

            $code .= BlockBuilderUtility::tab(2) . '<div class="form-group entries-actions">' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(3) . '<button type="button" class="btn btn-primary entries-action-button js-add-entry" data-action="prepend"><?php echo t(\''.addslashes($postData['addAtTheTopLabel']).'\'); ?></button>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(3) . '<button type="button" class="btn btn-primary entries-action-button js-add-entry" data-action="append"><?php echo t(\''.addslashes($postData['addAtTheBottomLabel']).'\'); ?></button>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(3) . '<span class="entries-actions-links">' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(4) . '<a href="#" class="entries-action-link js-expand-all"><i class="fa fa-plus-square-o"></i> <?php echo t(\''.addslashes($postData['expandAllLabel']).'\'); ?></a>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(4) . '<a href="#" class="entries-action-link js-collapse-all"><i class="fa fa-minus-square-o"></i> <?php echo t(\''.addslashes($postData['collapseAllLabel']).'\'); ?></a>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(3) . '</span>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(2) . '</div>' . PHP_EOL . PHP_EOL;

        }

        if ( ! empty($postData['entries'])) {

            $code .= BlockBuilderUtility::tab(2) . '<script type="text/template" class="js-entry-template">' . PHP_EOL . PHP_EOL;

            $code .= BlockBuilderUtility::tab(3) . '<div class="well entry js-entry" data-position="<%=position%>">' . PHP_EOL . PHP_EOL;

            $code .= BlockBuilderUtility::tab(4) . '<div class="entry-header">' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(5) . '<div class="entry-header-action entry-header-remove-entry js-remove-entry" data-confirm-text="<?php echo t(\'Are you sure?\'); ?>"><i class="fa fa-times"></i></div>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(5) . '<div class="entry-header-action entry-header-move-entry js-move-entry"><i class="fa fa-arrows"></i></div>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(5) . '<div class="entry-header-action entry-header-toggle-entry js-toggle-entry" data-action="collapse"><i class="fa fa-minus-square-o"></i></div>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(5) . '<div class="entry-header-title">' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(6) . '<span class="js-entry-title">' . PHP_EOL;
            if ($postDataSummary['entryTitleSource']) {
                $code .= BlockBuilderUtility::tab(7) . '<% if (' . $postDataSummary['entryTitleSource']. ') { %>' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(8) . '<%=' . $postDataSummary['entryTitleSource'] . '%>' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(7) . '<% } else { %>' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(8) . '#<%=position%>' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(7) . '<% } %>' . PHP_EOL;
            } else {
                $code .= BlockBuilderUtility::tab(7) . '#<%=position%>' . PHP_EOL;
            }
            $code .= BlockBuilderUtility::tab(6) . '</span>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(4) . '</div>' . PHP_EOL . PHP_EOL;

            $code .= BlockBuilderUtility::tab(4) . '<div class="entry-content js-entry-content">' . PHP_EOL . PHP_EOL;


            $previousFieldTypeHadMultipleFields = false;
            $i = 0;

            foreach ($postData['entries'] as $k => $v) {

                $i++;

                $required = !empty($v['required']) ? ' *' : '';

                // Horizontal line (always)
                if ($postData['entryFieldsDivider']=='always' AND $i!=1) {
                    $code .= BlockBuilderUtility::tab(5).'<hr/>'.PHP_EOL.PHP_EOL;
                }

                // Horizontal line (smart)
                if ($postData['entryFieldsDivider'] == 'smart' AND $i!=1) {
                    if (
                        !empty($v['linkFromSitemapShowEndingField']) OR
                        !empty($v['linkFromSitemapShowTextField']) OR
                        !empty($v['linkFromSitemapShowTitleField']) OR
                        !empty($v['linkFromFileManagerShowTextField']) OR
                        !empty($v['linkFromFileManagerShowTitleField']) OR
                        !empty($v['externalLinkShowTextField']) OR
                        !empty($v['externalLinkShowTitleField']) OR
                        !empty($v['imageShowAltTextField'])
                    ) {
                        $code .= BlockBuilderUtility::tab(5) . '<hr/>' . PHP_EOL . PHP_EOL;
                        $previousFieldTypeHadMultipleFields = true;
                    } else {
                        if ($previousFieldTypeHadMultipleFields) {
                            $code .= BlockBuilderUtility::tab(5) . '<hr/>' . PHP_EOL . PHP_EOL;
                            $previousFieldTypeHadMultipleFields = false;
                        }
                    }
                }

                // Field types
                if ($v['fieldType'] == 'text_field') {

                    $jsEntryTitleSource = ($postDataSummary['entryTitleSource']==$v['handle']) ? ' js-entry-title-source' : false;

                    $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="control-label"><?php echo t(\'' . addslashes($v['label']) . '\'); ?>' . $required . '</label>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(6) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" value="<%=' . $v['handle'] . '%>" class="form-control'.$jsEntryTitleSource.'" maxlength="255" />' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;

                }

                if ($v['fieldType'] == 'textarea') {

                    $jsEntryTitleSource = ($postDataSummary['entryTitleSource']==$v['handle']) ? ' js-entry-title-source' : false;

                    $height = !empty($v['textareaHeight']) ? ' style="height: '.$v['textareaHeight'].'px;"' : false;

                    $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="control-label"><?php echo t(\'' . addslashes($v['label']) . '\'); ?>' . $required . '</label>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(6) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(6) . '<textarea id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="form-control'.$jsEntryTitleSource.'"'.$height.'><%=' . $v['handle'] . '%></textarea>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;

                }

                if ($v['fieldType'] == 'wysiwyg_editor') {

                    $height = !empty($v['wysiwygEditorHeight']) ? $v['wysiwygEditorHeight'] : false;

                    $code .= BlockBuilderUtility::tab(5) . '<div class="form-group js-custom-editor-height-<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']-<?php echo $uniqueID; ?>">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="control-label"><?php echo t(\'' . addslashes($v['label']) . '\'); ?>' . $required . '</label>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(6) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(6) . '<textarea style="display: none;" class="js-editor-content" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']"><%=' . $v['handle'] . '%></textarea>' . PHP_EOL;
                    if ($height) {
                        $code .= BlockBuilderUtility::tab(6) . '<style>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(7) . '<?php if ($wysiwygEditor==\'ckeditor\'): ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(7) . '.js-custom-editor-height-<?php echo str_replace(array(\'[\', \']\'), array(\'\[\', \'\]\'), $view->field(\'entry\').\'[<%=position%>][' . $v['handle'] . ']\'); ?>-<?php echo $uniqueID; ?> .cke_contents {' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(8) . 'height: '.$height.'px !important;' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(7) . '}' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(7) . '<?php else :?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(7) . '.js-custom-editor-height-<?php echo str_replace(array(\'[\', \']\'), array(\'\[\', \'\]\'), $view->field(\'entry\').\'[<%=position%>][' . $v['handle'] . ']\'); ?>-<?php echo $uniqueID; ?> .redactor-editor {' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(8) . 'min-height: '.$height.'px !important;' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(7) . '}' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(7) . '<?php endif; ?>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '</style>' . PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;

                }

                if ($v['fieldType'] == 'select_field') {

                    $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="control-label"><?php echo t(\'' . addslashes($v['label']) . '\'); ?>' . $required . '</label>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(6) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(6) . '<select id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="form-control">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(7) . '<?php foreach ($entry_' . $v['handle'] . '_options as $k => $v): ?>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(8) . '<option value="<?php echo $k; ?>" <% if (' . $v['handle'] . '==\'<?php echo $k; ?>\') { %>selected="selected"<% } %> ><?php echo h($v); ?></option>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(7) . '<?php endforeach; ?>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '</select>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;

                }

                if ($v['fieldType'] == 'link_from_sitemap') {

                    $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="control-label"><?php echo t(\''.addslashes($v['label']).'\'); ?>' . $required . '</label>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(6) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(6) . '<div class="js-page-selector" data-input-name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" data-collection-id="<%=' . $v['handle'] . '%>"></div>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;

                    if ( ! empty($v['linkFromSitemapShowEndingField'])) {
                        $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_ending]" class="control-label"><?php echo t(\'' . addslashes($postData['urlEndingLabel']) . '\'); ?></label>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<p class="small text-muted"><?php echo t(\''.addslashes($postData['urlEndingHelpText']).'\'); ?></p>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_ending]" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_ending]" value="<%=' . $v['handle'] . '_ending%>" class="form-control" maxlength="255" />' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                    if ( ! empty($v['linkFromSitemapShowTextField'])) {
                        $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_text]" class="control-label"><?php echo t(\'' . addslashes($postData['textLabel']) . '\'); ?></label>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_text]" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_text]" value="<%=' . $v['handle'] . '_text%>" class="form-control" maxlength="255" />' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                    if ( ! empty($v['linkFromSitemapShowTitleField'])) {
                        $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_title]" class="control-label"><?php echo t(\'' . addslashes($postData['titleLabel']) . '\'); ?></label>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_title]" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_title]" value="<%=' . $v['handle'] . '_title%>" class="form-control" maxlength="255" />' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                }

                if ($v['fieldType'] == 'link_from_file_manager') {

                    $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="control-label"><?php echo t(\'' . addslashes($v['label']) . '\'); ?>' . $required . '</label>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(6) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(6) . '<div class="ccm-file-selector js-file-selector"' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(7) . 'data-input-name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']"' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(7) . 'data-file-id="<%=' . $v['handle'] . '%>"' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '></div>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;

                    if ( ! empty($v['linkFromFileManagerShowTextField'])) {
                        $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_text]" class="control-label"><?php echo t(\'' . addslashes($postData['textLabel']) . '\'); ?></label>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_text]" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_text]" value="<%=' . $v['handle'] . '_text%>" class="form-control" maxlength="255" />' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                    if ( ! empty($v['linkFromFileManagerShowTitleField'])) {
                        $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_title]" class="control-label"><?php echo t(\'' . addslashes($postData['titleLabel']) . '\'); ?></label>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_title]" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_title]" value="<%=' . $v['handle'] . '_title%>" class="form-control" maxlength="255" />' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                }

                if ($v['fieldType'] == 'external_link') {

                    $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">'. PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="control-label"><?php echo t(\'' . addslashes($v['label']) . '\'); ?>' . $required . '</label>'. PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(6) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" value="<%=' . $v['handle'] . '%>" class="form-control" maxlength="255" />'. PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;

                    if ( ! empty($v['externalLinkShowTextField'])) {
                        $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">'. PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_text]" class="control-label"><?php echo t(\'' . addslashes($postData['textLabel']) . '\'); ?></label>'. PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_text]" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_text]" value="<%=' . $v['handle'] . '_text%>" class="form-control" maxlength="255" />'. PHP_EOL;
                        $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                    if ( ! empty($v['externalLinkShowTitleField'])) {
                        $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">'. PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_title]" class="control-label"><?php echo t(\'' . addslashes($postData['titleLabel']) . '\'); ?></label>'. PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_title]" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_title]" value="<%=' . $v['handle'] . '_title%>" class="form-control" maxlength="255" />'. PHP_EOL;
                        $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                }

                if ($v['fieldType'] == 'image') {

                    $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="control-label"><?php echo t(\'' . addslashes($v['label']) . '\'); ?>' . $required . '</label>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(6) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(6) . '<div class="ccm-file-selector js-file-selector"' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(7) . 'data-input-name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']"' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(7) . 'data-file-id="<%=' . $v['handle'] . '%>"' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '></div>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;

                    if ( ! empty($v['imageShowAltTextField'])) {
                        $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_alt]" class="control-label"><?php echo t(\'' . addslashes($postData['altTextLabel']) . '\'); ?></label>' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_alt]" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . '_alt]" value="<%=' . $v['handle'] . '_alt%>" class="form-control" maxlength="255" />' . PHP_EOL;
                        $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;
                    }

                }

                if ($v['fieldType'] == 'html_editor') {

                    $height = !empty($v['htmlEditorHeight']) ? $v['htmlEditorHeight'] : 250;

                    $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="control-label"><?php echo t(\'' . addslashes($v['label']) . '\'); ?>' . $required . '</label>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(6) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }
                    $code .= BlockBuilderUtility::tab(6) . '<div id="entry-<%=position%>-' . $v['handle'] . '-html-editor-<?php echo $uniqueID; ?>" data-textarea-id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="js-html-editor" style="height: '.$height.'px; border: 1px solid #dedede;"><%=' . $v['handle'] . '%></div>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<textarea id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="form-control" style="display: none;"><%=' . $v['handle'] . '%></textarea>' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;

                }

                if ($v['fieldType'] == 'date_picker') {

                    $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">' . PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<label for="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" class="control-label"><?php echo t(\'' . addslashes($v['label']) . '\'); ?>' . $required . '</label>' . PHP_EOL;
                    if ( ! empty($v['helpText'])) {
                        $code .= BlockBuilderUtility::tab(6) . '<p class="small text-muted"><?php echo t(\''.$v['helpText'].'\'); ?></p>'.PHP_EOL;
                    }

                    $code .= BlockBuilderUtility::tab(5) . '<div class="form-group">'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . ']" value="<%=' . $v['handle'] . '%>" class="form-control js-entry-' . $v['handle'] . '-<%=position%>" style="display: none;"/>'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(6) . '<input type="text" id="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . 'Displayed]" name="<?php echo $view->field(\'entry\'); ?>[<%=position%>][' . $v['handle'] . 'Displayed]" value="<%=' . $v['handle'] . 'Displayed%>" class="form-control js-entry-date-displayed" data-date-format="<?php echo Core::make(\'helper/date\')->getJQueryUIDatePickerFormat(\'' . $v['datePickerPattern'] . '\'); ?>" data-target-field="' . $v['handle'] . '" data-position="<%=position%>" />'.PHP_EOL;
                    $code .= BlockBuilderUtility::tab(5) . '</div>'.PHP_EOL;

                    $code .= BlockBuilderUtility::tab(5) . '</div>' . PHP_EOL . PHP_EOL;

                }

            }

            $code .= BlockBuilderUtility::tab(4) . '</div>' . PHP_EOL . PHP_EOL;

            $code .= BlockBuilderUtility::tab(3) . '</div>' . PHP_EOL . PHP_EOL;

            $code .= BlockBuilderUtility::tab(2) . '</script>' . PHP_EOL . PHP_EOL;

        }


        if ( ! empty($postData['entries'])) {

            $code .= BlockBuilderUtility::tab(2) . '<script type="text/template" class="js-template-no-entries">' . PHP_EOL . PHP_EOL;

            $code .= BlockBuilderUtility::tab(3) . '<div class="alert alert-info js-alert"><?php echo t(\''.addslashes($postData['noEntriesFoundLabel']).'\'); ?></div>' . PHP_EOL . PHP_EOL;

            $code .= BlockBuilderUtility::tab(2) . '</script>' . PHP_EOL . PHP_EOL;

        }

        if ( ! empty($postData['entries'])) {

            $code .= BlockBuilderUtility::tab(2) . '<script>' . PHP_EOL . PHP_EOL;

            if ($postDataSummary['wysiwygEditorUsed_entry']) {
                $code .= BlockBuilderUtility::tab(3) . 'var CCM_EDITOR_SECURITY_TOKEN = \'<?php echo Core::make(\'helper/validation/token\')->generate(\'editor\'); ?>\';' . PHP_EOL . PHP_EOL;

                $code .= BlockBuilderUtility::tab(3) . '<?php if ($wysiwygEditor==\'ckeditor\'): ?>' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(4) . 'var activateEditor = <?php echo Core::make(\'editor\')->outputStandardEditorInitJSFunction(); ?>;' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(3) . '<?php else: ?>' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(4) . 'var activateEditor = function (id) {' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(5) . '$(id).redactor({' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(6) . '\'minHeight\': 300,' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(6) . '\'concrete5\': {filemanager: <?php echo Core::make(\'editor\')->allowFileManager(); ?>, sitemap: <?php echo Core::make(\'editor\')->allowSitemap(); ?>, lightbox: true},' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(6) . '\'plugins\': [\'concrete5lightbox\', \'undoredo\', \'specialcharacters\', \'table\', \'concrete5magic\']' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(5) . '});' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(4) . '};' . PHP_EOL;
                $code .= BlockBuilderUtility::tab(3) . '<?php endif; ?>' . PHP_EOL . PHP_EOL;
            }
            $code .= BlockBuilderUtility::tab(3) . 'Concrete.event.publish(\'open.block.'.$postDataSummary['blockHandleDashed'].'\', {' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(4) . '\'uniqueID\' : \'<?php echo $uniqueID; ?>\'' . PHP_EOL;
            $code .= BlockBuilderUtility::tab(3) . '});' . PHP_EOL . PHP_EOL;

            $code .= BlockBuilderUtility::tab(2) . '</script>' . PHP_EOL . PHP_EOL;

        }

        if ( ! empty($postData['entries'])) {
            $code .= BlockBuilderUtility::tab(1).'</div>'.PHP_EOL.PHP_EOL;
        }

        if ( ! empty($postData['requiredFieldsLabel']) AND (!empty($postDataSummary['requiredFields']) OR !empty($postDataSummary['requiredEntryFields']))) {
            $code .= BlockBuilderUtility::tab(1) . '<hr/>' . PHP_EOL . PHP_EOL;
            $code .= BlockBuilderUtility::tab(1) . '<p class="small text-muted">* <?php echo t(\''.addslashes($postData['requiredFieldsLabel']).'\'); ?></p>' . PHP_EOL . PHP_EOL;
        }
        $code .= '</div>';


        $fileService = new FileService();
        $fileService->append($postDataSummary['blockPath'] . DIRECTORY_SEPARATOR . $filename, $code);

    }

}