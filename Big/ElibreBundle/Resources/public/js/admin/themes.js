function initThemesTree(treeContainer, treeData) {
//  alert(treeData[10].data.code);
  treeContainer.jstree({
    "core": {
      "themes": {
        "name": "default",
        "url": true,
        /* TODO: make paths more nice (through assets of Symfony) */
        "dir": pageRoot + '../../../../bundles/bigelibre/js/jstree/themes',
        "stripes": true,
      },
      'check_callback': function(operation, node, node_parent, node_position) {
        // operation can be 'create_node', 'rename_node', 'delete_node', 'move_node' or 'copy_node'
        // in case of 'rename_node' node_position is filled with the new node name
        return (operation === 'move_node') || (operation === 'create_node')
                || (operation === 'delete_node')
                ? true : false;
      },
      data: treeData,
    },
    plugins: ["dnd", "state", "checkbox_", "types", "grid"],
    "defaults": {
      "state": {
        "events": "changed.jstree open_node.jstree close_node.jstree",
      },
      "checkbox": {
        "whole_node": false,
        "keep_selected_style": false,
      }
    },
    grid: {
      columns: [
        {width: 300, header: "Themes", resizable: true},
//        {width: 100, header: "Code", source: "attr2", value: "code"},
        {width: 24, header: "Active", source: "callback", func: getActiveAction, value: "id"},
        {width: 24, header: "Edit", source: "callback", func: getEditAction, value: "id"},
        {width: 24, header: "Delete", source: "callback", func: getDeleteAction, value: "id"},
      ],
      showHeaders: false
    }
  });

  treeContainer.on('move_node.jstree', function(e, data) {
//    var tree = treeContainer.jstree(true);
//    alert(JSON.stringify(tree.get_json(tree.get_node('#'))));

//    $('#event_result').html('Moved <b>' + data.node + '</b> <b>' + data.old_parent + '</b> to <b>' + data.parent + '</b> at ' + data.position);
//    $('#event_result').html('Theme was modifi');
    var act = new ThemeAction("move", data.node.id);
    act.moveOldParentID = data.old_parent;
    act.moveNewParentID = data.parent;
    act.moveNewOrder = data.position;
    ActionsQueue.add(act);

    $('#save_action').show();
  });

  //initActions(treeContainer);  

}

function getActiveAction(themeID) {
  var actionIcon = "";
  var allThemes = themesData;
  if (allThemes) {
    var theme = findTheme(themeID, allThemes);
    if (theme) {
      if (theme.active) {
        actionIcon = '<img src="' + pageRoot + '../../../../bundles/bigelibre/images/admin/eye.png">';
      } else {
        actionIcon = '<img src="' + pageRoot + '../../../../bundles/bigelibre/images/admin/eye_2.png">';
      }
    }
  }
  return '<a href="' + pageRoot + '/active?theme=' + themeID + '">' + actionIcon + '</a>';
}

function getDeleteAction(themeID) {
  var themeTitle = "";
  var allThemes = themesData;
  if (allThemes) {
    var theme = findTheme(themeID, allThemes);
    if (theme) {
      themeTitle = theme.text + '(' + theme.code + ')';
    }
  }
  return '<a href="' + pageRoot + '/delete?theme=' + themeID + '" onclick="return confirm(\'Delete ' + themeTitle + '?\');">\n\
          <img src="' + pageRoot + '../../../../bundles/bigelibre/images/admin/delete-2.png"></a>';
}

function getEditAction(themeID) {
  return '<a href="#" onclick="editThemeDlg(this, ' + themeID + ')">\n\
          <img src="' + pageRoot + '../../../../bundles/bigelibre/images/admin/edit.png"></a>';
}

//function getNewAction(themeID) {
//  return '<a href="#" onclick="createRootThemeDlg(this);">\n\
//          <img src="../../bundles/bigelibre/images/admin/folder-new.png"></a>';
//}

function findTheme(themeID, allThemes) {
  var theme = null;
  for (var i in allThemes) {
//    alert(t);
    if (allThemes[i].id == themeID) {
      theme = allThemes[i];
      break;
    }
  }
  return theme;
}

function createRootThemeDlg(e, treeContainer, sender) {
  var tree = treeContainer.jstree(true);
  //alert(tree.get_selected());
  $.fn.custombox({
    effect: 'fadein',
    overlayClose: false,
    url: pageRoot + '/edit',
    complete: function() {
      $('#form_parent_id').val(tree.get_selected());
    }
  });
  e.preventDefault();
}

function editThemeDlg(e, themeID) {
//  var tree = treeContainer.jstree(true);
  //alert(tree.get_selected());
  $.fn.custombox({
    effect: 'fadein',
    overlayClose: false,
    url: pageRoot + '/edit?theme=' + themeID,
//    complete: function() {
//      $('#form_parent_id').val(tree.get_selected());
//    }
  });
  e.preventDefault();
}

function createRootTheme(treeContainer) {
  createNewTheme(treeContainer, '#');
  //alert(tree.create_node(tree.get_selected, 'new node'));
//  alert(tree.create_node(sel, {"type":"file"}));
}

function createNewTheme(treeContainer, parent) {
  var tree = treeContainer.jstree(true);
  if (!parent || (parent == 0))
    parent = '#';
  if (tree) {
//    var parentObj = tree.get_node(parent);
//    if (parentObj) {
    tree.create_node(parent, 'NewTheme');
//    }
  }
}

function ThemeAction(actType, themeID) {
  this.actionType = actType;
  this.modifiedThemeID = themeID;
  // action dependent properties
  this.moveNewOrder = "";
  this.moveOldParentID = "";
  this.moveNewParentID = "";
}

var ActionsQueue = {
  queue: new Array(),
  add: function(action) {
    this.queue.push(action);
  },
  clear: function() {
    this.queue = new Array();
  },
  getArray: function() {
    return this.queue;
  }
}

function performActions() {
//  var tree = treeContainer.jstree(true);
//  alert(JSON.stringify(tree.get_json(tree.get_node('#'))));
//alert(ActionsQueue.getArray());
//var arr = { name: "John", time: "2pm" };
//window.location.replace('');
//  ActionsQueue.clear();
//  $('#save_action').hide();

  var url = pageRoot + '/savebatch';

//  $.ajax(url,
//          {actions: ActionsQueue.getArray()}
//  ).done(function(data, textStatus, jqXHR) {
////    alert('ajax data (' + textStatus + '): ' + JSON.stringify(data));
//    window.location.reload();
//  });

  $.ajax({
    type: "POST",
    url: url,
    data: {actions: ActionsQueue.getArray()},
    error: function(data, textStatus, jqXHR) {
      alert('ajax data (' + textStatus + '): ' + JSON.stringify(data));
    },
    success: function(data, textStatus, jqXHR) {
      //alert('ajax data (' + textStatus + '): ' + JSON.stringify(data));
      window.location.reload();
    }
  });

}