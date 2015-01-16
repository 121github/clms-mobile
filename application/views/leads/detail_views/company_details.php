<div class="compdetails">
    <div class="ui-grid-a">
        <?php $this->firephp->log($companyDetails); foreach ($companyDetails as $key => $info): ?>
            <?php if (!empty($info['value'])): ?>
                <div class="ui-block-a"><?php echo $info['label']; ?></div>
                <div class="ui-block-b">
                    <?php if ($key === 'website'): ?>
                        <a href="<?php echo $info['value']; ?>" target="_blank" rel="external" data-ajax="false">
                            <?php echo $info['value']; ?>
                        </a>  
                        <?php elseif($key === 'turnover_validated' || $key === 'employees_validated' || $key === 'consent_to_contact'): ?>
                        <img src="<?php echo base_url() ?>/assets/img/small_green_tick.gif"/>
                    <?php else: ?>
                        <?php echo $info['value']; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <a href="#" class="pull-right edit" data-role="button" data-inline="true" data-theme="b" data-mini="true">Edit</a>
    <div class="float-push"></div>
</div>

<form class="compdetails-edit-form hidden">
    <input type="hidden" name="urn" class="urn" value="<?php echo $urn; ?>">
    <div class="ui-grid-a">
        <?php foreach ($companyDetails as $key => $info): ?>
            <div class="ui-block-a"><?php echo $info['label']; ?></div>
            <div class="ui-block-b">
                <?php if (isset($info['options'])): ?>
                    <select name="<?php echo $key; ?>">
                        <option value="no_selection_made">Please make a selection...</option>
                        <?php foreach ($info['options'] as $option): ?>
                            <option value="<?php echo $option; ?>" <?php if ($info['value'] == $option) { echo "selected='selected'"; }?>>
                                <?php echo $option; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif(isset($info['type']) && $info['type'] === 'checkbox'): ?>
                    <fieldset data-role="controlgroup" style="padding: 5px;">
                        <input name="<?php echo $key; ?>" id="<?php echo $key; ?>" <?php echo $info['value'] == 1 ? 'checked' : ''; ?> type="checkbox" value="1">
                        <label for="<?php echo $key; ?>"><?php echo $info['label']; ?></label>
                    </fieldset>
                <?php else: ?>
                    <input name="<?php echo $key; ?>" value="<?php echo $info['value']; ?>" 
                        type="<?php echo isset($info['type']) ? $info['type'] : 'text'; ?>" <?php if (isset($info['attr'])) { echo $info['attr']; } ?>>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="pull-right" data-role="controlgroup" data-type="horizontal" data-mini="true">
        <a href="#" data-role="button" data-theme="c" class="cancel">Cancel</a>
        <a href="#" data-role="button" data-theme="b" class="save">Save</a>
    </div>
    <div class="float-push"></div>
</form>