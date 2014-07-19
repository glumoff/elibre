//    $('#openFMBtn').on('click', function(e) {
//      openFileManager(e, $('#form_path'));
//      e.preventDefault();
//    });

//alert('included');

function openFileManager(e, target, fmURL) {
//  alert('openFileManager');
//  alert(Routing.generate('big_elibre_fm_actions'));

//  
//  alert(target);
  if (target) {
    $.fn.custombox({
      effect: 'fadein',
      zIndex: 20000,
//      overlayClose: false,
      url: fmURL,
//      overlay: false,
//      eClose: 'closeBtn',
      complete: function() {
//        new FileManager().setTargetControl(target);
        new FileManager().onSelect = function (selectedValue) {
//          alert('FM.onSelect');
          target.val(selectedValue);
          propouseNameFromFile(selectedValue);
        }
//        new FileManager().parentBox = this;
      }
    });
  }
  e.preventDefault();

//  alert(tree.get_selected());
//  


//  return;
//  $.fn.custombox(sender, {

}

function propouseNameFromFile(fpath) {
  var titleWidget = $('#form_title');
  if (titleWidget && !titleWidget.val()) {
    var title;
    // get basename
    var p = fpath.lastIndexOf('/', fpath.length - 1);
    if (p >= 0) {
      title = fpath.substring(p + 1);
    }
    else {
      title = fpath;
    }
    // get rid of extension
    p = title.lastIndexOf('.', title.length - 1);
    if (p >= 0) {
      title = title.substring(0, p);
    }
    titleWidget.val(title);
  }
}

function deleteDocConfirm(targetURL, dlgWidget) {
  var res = false;
  //res = confirm('Are you sure to delete document?');
  if (dlgWidget) {
    dlgWidget.dialog('open');
  }
  return res;
}