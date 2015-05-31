// init function
//$(function() {
//  //var FM = new FileManager();
//  new FileManager().init();
//
//  $("#fileuploader").uploadFile({
//    url: "http://172.18.1.12:8080/app_dev.php/fm/upload",
////    fileName: "myfile",
//    onSubmit: function(files) {
//      this.formData.path = new FileManager().currentPath;
//      this.data.path = new FileManager().currentPath;
//    },
//    onSuccess: function(files, data, xhr)
//    {
//      new FileManager().refreshFileList();
//    },
//    dynamicFormData: function()
//    {
//      var data = {path: new FileManager().currentPath};
//      return data;
//    },
//  });
//});

//var testSingleton = {
//  tt: "default"
//};
//
//var newFileManager = (function (window, undefined) {
//  var instance = null;
//
//
//  function initInstance() {
//    
//    var testVar = 'default';
//    
//    function myMethod() {
//      alert('my method');
//    }
//
//    function myOtherMethod() {
//      alert('my other method');
//    }
//
//    return {
//      someMethod: myMethod,
//      someOtherMethod: myOtherMethod,
//      testVar: testVar
//    };
//
//  }
//  ;
//
//  function getInstance() {
//    if (!instance) {
//      instance = new initInstance();
//    }
//    return instance;
//  }
//
//  return {
//    getInstance: getInstance
//  };
//
//})(window);


function FileManager() {
  // Singleton
  if (!FileManager.__instance)
    FileManager.__instance = this;
  else
    return FileManager.__instance;

  this.currentPath = '';

  this.testVar = 'testVar';

//  console.log('FileManager(): ' + new FileManager().onSelect);

  this.init = function () {
    this.currentPath = $('#currentPath').text();
    this.refreshFileList();
  };

  this.refreshFileList = function () {
    //var url = 'http://172.18.1.12:8080/app_dev.php/fm/getlist';
//    var url = Routing.generate('big_elibre_fm_actions', { action: 'getlist' });
    var url = Routing.generate('big_elibre_fm_actions', {action: 'getlist'});
//    alert(url);
//    var thisFM = this;
    $.ajax({
      type: "GET",
      url: url,
      data: {'path': this.currentPath},
      error: function (data, textStatus, jqXHR) {
        alert('ajax data (' + textStatus + '): ' + JSON.stringify(data));
      },
      success: function (data, textStatus, jqXHR) {
        var data = eval(data);
        var curFile/*, curAction,
         ind = 1,
         curLi*/;
        $('.FMFileList ul').empty();

        // go up link
        $('.FMFileList ul').append('<li class="dirUp"><h4>\n\
                                        <a href="#" class="text-info">\n\
                                          <span class="glyphicon glyphicon-arrow-up"></span> ..</a>\n\
                                      </h4></li>\n');
//        alert(JSON.stringify(data));
        var iconHref, closeModal;
        for (var i in data) {
          curFile = data[i];
//          alert(curFile.type);
          if (curFile.type == 'dir') {
            iconHref = 'glyphicon glyphicon-folder-open';
            closeModal = '';
          }
          else {
            iconHref = 'glyphicon glyphicon-file';
            closeModal = ' data-dismiss=\"modal\"';
          }
          if (curFile.fname) {
//                                                    <input type="checkbox" value="' + curFile.fpath + '">\n\
//                                        <a href="' + curFile.fpath + '">[' + curFile.type + ']</a>\n\
            $('.FMFileList ul').append('<li class="' + curFile.type + '">\n\
                                        <a href="' + curFile.fpath + '"' + closeModal + ' class="text-info">\n\
                                          <span class="' + iconHref + '"></span>&nbsp;&nbsp;' + curFile.fname + '\
                                        </a>\n\
                                      </li>\n');
          }
        }
//        console.log('fm.js: ' + new FileManager().onSelect);
        $('.FMFileList li.file a').on('click', function (e) {
          e.preventDefault();
          if (jQuery.isFunction(new FileManager().onSelect)) {
            new FileManager().onSelect($(this).attr('href'));
          }
//          console.log($('#fmModal').modal);
//          $('#fmModal').modal('hide');
//          $.fn.custombox('close');
//          var target = new FileManager().targetControl;
//          if (target) {
//            target.val($(this).attr('href'));
//            $.fn.custombox('close');
//          }
//          alert(new FileManager().parentBox);
//          if (new FileManager().parentBox) {
//            new FileManager().parentBox._close();
//            for (var i in new FileManager().parentBox) {
//              alert(i +' = ' + new FileManager().parentBox[i]);
//            }
//          }
        });
        $('.FMFileList li.dir a').on('click', function (e) {
//          alert($(this).attr('href') + '/');
          new FileManager().changeDir($(this).attr('href') + DIRECTORY_SEPARATOR);
          e.preventDefault();
        });
        $('.FMFileList li.dirUp a').on('click', function (e) {
          new FileManager().goUp();
          e.preventDefault();
        });

      }
    });
  };
  this.changeDir = function (newPath) {
    this.currentPath = newPath;
    $('#currentPath').text(newPath);
    this.refreshFileList();
  };
  this.goUp = function () {
    var newPath;
    var p = this.currentPath.lastIndexOf(DIRECTORY_SEPARATOR, this.currentPath.length - 2); // -2 to cut / at the end
    if (p >= 0) {
      newPath = this.currentPath.substring(0, p) + DIRECTORY_SEPARATOR;
      //alert(newPath);
    } else {
      newPath = '/';
    }
    this.changeDir(newPath);
  };
  this.uploadDialog = function () {
    alert('uploadDialog');
  };
  this.delete = function () {
    var itemsToDel = new Array(),
            itemsToDelStr = '',
            itemsToDelCount = 0;
    $('input[type=checkbox]').each(function () {
      if (this.checked) {
        itemsToDelCount++;
        itemsToDelStr += this.value + ', '
      }
    });
    if (itemsToDelCount) {
      if (confirm('You choose to delete ' + itemsToDelCount + ' item(s):\n' + itemsToDelStr + '\n\nAre you sure ?')) {
        //ajax call here
      }
    }
  };
//  this.targetControl = '';
//  this.parentBox = '';
//  this.setTargetControl = function(target) {
//    this.targetControl = target;
//  };

  this.onSelect; // callback function
}
