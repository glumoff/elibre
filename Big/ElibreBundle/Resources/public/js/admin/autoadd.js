function getAutoDocManager() {
  var adm = $('#autoAddModal').first().data('manager');
  if (!adm) {
    adm = new AutoDocManager();
    $('#autoAddModal').first().data('manager', adm);
  }
  return adm;
}

var AutoDocManager = (function () {
  var adm = {};

  var themeCode = null;

  adm.init = function (theme_code) {
    $('#notFoundMessage').hide();
    setThemeCode(theme_code);
    adm.refreshFileList();
  };

  var setThemeCode = function (theme_code) {
    themeCode = theme_code;
  };


  adm.refreshFileList = function () {
//    console.log('refreshFileList: ' + themeCode);
    $('#notFoundMessage').hide();
    var url = Routing.generate('big_elibre_admin', {'mode': 'docs', 'action': 'autolist', 'theme': themeCode});
    $.getJSON(url, function (data) {
      var target = $('#AutoList');
      target.html('');

      $.each(data, function (key, val) {
        $('<div class="checkbox"> \
            <label>\
              <input type="checkbox" value="' + val.fname + '"> ' + val.fname + '\
            </label>\
          </div>').appendTo(target);
//        $('#loadingIndicator').hide();
//        console.log(val);
      });
      console.log(0 == data.length);
      if (0 == data.length) {
        $('#notFoundMessage').show();
      }

    });
  };

  adm.saveBatch = function () {
    var fileNameInputs = $('#AutoList').find(':checkbox:checked');
    var flist = new Array();
//    console.log(fileNameInputs);
    $.each(fileNameInputs, function (key, inpt) {
      flist.push($(inpt).val());
    });
    console.log(flist);
    var url = Routing.generate('big_elibre_admin', {'mode': 'docs', 'action': 'autosave', 'theme': themeCode});
    if (flist.length > 0) {
      $.post(url, {'flist': flist}, function (data) {
//        alert('success: ' + data);
        window.location.href = Routing.generate('big_elibre_theme', {'action': 'view', 'theme_code': themeCode});
      })
              .fail(function () {
                alert('error occured!');
              });
    }
    ;
  };

//  setThemeCode(theme_code);
//  adm.init(theme_code);

  return adm;
});
