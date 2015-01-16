<div data-overlay-theme="a" data-role="popup" id="popupEditApp" class="edit-appointment-popup" data-position-to="window">
  <a href="#" data-rel="back" data-role="button" data-theme="d" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
  <div class="popup-header">
    <h3>Edit Appointment</h3>
  </div>
  <div class="popup-content">
    <form class="appointment-form">
      <input name="id" class="appointment_id" type="hidden" value="0">
      <input type="hidden" name="fn" class="fn" value="edit">
      <input name="urn" class="urn" type="hidden" value="<?php
      if (!empty($urn)) {
        echo $urn;
      }
      ?>" >

      <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true"> 
        <legend>Appointment type</legend>
        <input type="radio" name="status" id="bde" value="BDE" checked="checked" />
        <label for="bde">BDE</label>
        <input type="radio" name="status" id="cae" value="Live"  />
        <label for="cae">CAE</label>
      </fieldset>
      <input type="hidden" name="manager" value="<?php echo $_SESSION['login'] ?>">
      <label for="title">Title</label>
      <input name="title" class="title" type="text" data-clear-btn="true">
      <div class="error-txt title pull-right">* Title is a required field</div>
      <label for="comments">Comments</label>
      <textarea cols="40" rows="8" name="comments" class="comments"></textarea>
      <div class="error-txt comments pull-right">* Comments is a required field</div>
      <label for="begin_date">Date</label>
      <input name="begin_date" class="begin_date" type="text" data-role="datebox" data-clear-btn="true" 
             data-options='{"mode":"calbox", "calUsePickers": true,"useNewStyle":true, "closeCallback":"showPopup", "closeCallbackArgs":["#<?php echo $pageId ?> .edit-appointment-popup"]}' />
      <div class="error-txt begin_date pull-right">* Date is a required field</div>
      <label for="start_time">Start Time</label>
      <input name="start_time" class="start_time" type="text" data-role="datebox" 
             data-options='{"mode":"timebox", "useNewStyle":true, "closeCallback":"showPopup", "closeCallbackArgs":["#<?php echo $pageId ?> .edit-appointment-popup"], "minuteStep":15}' data-clear-btn="true"/>
      <div class="error-txt start_time pull-right">* Start Time is a required field</div>
      <label for="finish_time">Finish Time</label>
      <input name="finish_time" class="finish_time" type="text" data-role="datebox" 
             data-options='{"mode":"timebox", "useNewStyle":true, "closeCallback":"showPopup", "closeCallbackArgs":["#<?php echo $pageId ?> .edit-appointment-popup"], "minuteStep":15}' data-clear-btn="true"/>
      <div class="error-txt finish_time pull-right">* Finish Time is a required field</div>
      <div class="float-push"></div>
    </form>
  </div>
  <div class="popup-footer">
    <div data-role="controlgroup" data-type="horizontal" align="right">
      <a data-theme="c" href="#" data-rel="back" data-role="button" data-inline="true" data-mini="true" class="cancel-btn">Cancel</a>
      <button data-theme="b" type="button" class="save" data-inline="true" data-mini="true">Save</button>
    </div>
  </div>
</div>