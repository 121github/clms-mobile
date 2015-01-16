<div data-role="popup" id="storage-actions" class="options-popup">
  <ul data-role="listview" data-inset="true" style="min-width:210px;" data-theme="b">
    <li><a href="#" class="view-record">View Record</a></li>
    <li><a class="update-server">Update Server</a></li>
    <li><a class="remove-item">Delete from storage</a></li>
  </ul>
</div>

<ul data-inset="true" data-role="listview" class="listview-white">
  <li style="padding: 5px">
    <fieldset class="ui-grid-a">
      <div class="ui-block-a">
        <a data-role="button" data-icon="delete" data-theme="d" class="clear-storage">Clear Storage</a></div>
      <div class="ui-block-b"> 
        <a data-role="button" data-icon="refresh" data-theme="d" class="update-server-all">Update Server</a>
      </div> 
    </fieldset>
  </li>
</ul>

<ul class="storage-ct" data-role="listview" data-inset="true" data-split-icon="gear" data-split-theme="a">
</ul>

<ul class="storage-empty" data-role="listview" data-inset="true" data-split-icon="gear" data-split-theme="a">
  <li><h3>Local storage is empty</h3></li>
</ul>

<script>
  $(document).on('pageinit', '#storage', function() {
    if (localStorage.getItem("pending") === null || localStorage.getItem("pending") === 'undefined') {
      var storage = Array();
      localStorage.setItem('pending', JSON.stringify(storage));
      $('.storage-empty').show();
    } else {
      var storage = JSON.parse(localStorage.getItem('pending'));
      if (storage.length === 0) {
        $('.storage-empty').show();
      } else {
        $('.storage-empty').hide();
        $.each(storage, function(i, item) {
            var requestType = item[0];
            var coname = item[3];
            var urn = item[1]['urn'];
            var timestamp = helper.timestamp_to_uk(item[2]);

          $('.storage-ct').append($('<li><a class="view-item" id="' + i + '"><h3>' + requestType + '</h3><p>' + coname + '</p><span class="ui-li-aside">' + timestamp + '</span></a><a href="#storage-actions" urn="' + urn + '" storage-id="' + i + '" class="storage-actions-btn" data-rel="popup" data-transition="pop">View Options</a></li>'));
        });

        $('.storage-ct').listview('refresh');
      }
    }
    $(document).on('click', '.view-item', function() {
      var id = $(this).attr('id');
      storage = JSON.parse(localStorage.getItem('pending'));
      var $popUp = $("<div/>", {'class': 'ui-content'}).popup({
        dismissible: false,
        overlyaTheme: "a",
        transition: "pop"
      }).append('<a class="ui-btn-right ui-btn ui-shadow ui-btn-corner-all ui-btn-icon-notext ui-btn-up-a" data-iconpos="notext" data-icon="delete" data-theme="a" data-role="button" data-rel="back" href="#" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" title="Close"><span class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">Close</span><span class="ui-icon ui-icon-delete ui-icon-shadow">&nbsp;</span></span></a>').append($('<table/>', {'id': 'storageDetails'})).on("popupafterclose", function() {
        $(this).remove();
      });
      $('#storageDetails').append('<tr><th>Field</th><th>New Value</th></tr>');
      $.each(storage[id][1], function(key, value) {
        $('#storageDetails').append('<tr><td>' + key + '</td><td>' + value + '</td></tr>');
      });

      $popUp.popup('open');
    });

    $(document).on('click', '.update-server', function() {
      var id = $('#storage-actions').attr('storage-id');
      var data = storage[id][1];
      var url = storage[id][4];
      $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: data,
        timeout: 10000,
        error: function(request, status, maybe_an_exception_object) {
          if (status === 'timeout')
            alert("Internet connection is down! The server could not be updated");
          $.mobile.loading('hide');
        },
        beforeSend: function() {
          $.mobile.loading('show');
        },
        success: function(data) {
          if (data.success) {
            //removes it from local storage if the server updated
            storage.splice(id, 1);
            localStorage.setItem('pending', JSON.stringify(storage));
            $('.storage-ct').find('a#' + id).closest('li').remove();
            $.mobile.loading('hide');

          } else {
            $.mobile.loading('hide');
            alert(data.message);
          }
        }
      });


    });


    $(document).on('click', '.storage-actions-btn', function() {
      var urn = $(this).attr('urn');
      var id = $(this).attr('storage-id');
      $('#storage-actions').attr('storage-id', id);
      $('#storage-actions').find('.view-record').attr('href', helper.baseUrl + 'leads/detail/' + urn);
    });
    
    $(document).on('click', '.clear-storage', function() {
      if (confirm('This will delete all entries in the local storage. Are you sure?'))
        var newStorage = new Array();
      localStorage.setItem('pending', JSON.stringify(newStorage));
      $('.storage-ct').empty();
      $('.storage-empty').show();
    });

    $(document).on('click', '.remove-item', function() {
      var id = $('#storage-actions').attr('storage-id');
      if (confirm('Are you sure you want to remove this action?'))
            storage.splice(id, 1);  
            localStorage.setItem('pending', JSON.stringify(storage));
            $('.storage-ct').find('a#' + id).closest('li').remove();
    });

    
    $(document).on('click', '.update-server-all', function() {
      if (confirm('This will run all updates in chronological order. Entries on matching records will be overwritten by the last entry. Continue?'))
        $.each(storage,function(i,item){ 
      var id = i; 
      var data = storage[id][1];
      var url = storage[id][4];
      $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: data,
        timeout: 10000,
        error: function(request, status, maybe_an_exception_object) {
          if (status === 'timeout')
            alert("Internet connection is down! The server could not be updated");
          $.mobile.loading('hide');
        },
        beforeSend: function() {
          $.mobile.loading('show');
        },
        success: function(data) {
          if (data.success) {
            //removes it from local storage if the server updated
            storage.splice(id, 1);
            localStorage.setItem('pending', JSON.stringify(storage));
            $('.storage-ct').find('a#' + id).closest('li').remove();
            $.mobile.loading('hide');

          } else {
            $.mobile.loading('hide');
            alert(data.message);
          }
        }
      });
  
  });
            
    });


  });
</script>