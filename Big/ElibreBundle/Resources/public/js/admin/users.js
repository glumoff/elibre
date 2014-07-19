$(function() {
//  alert(Routing.generate('big_elibre_admin', {mode: 'user', action: 'list'}));
  $('#UsersTableContainer').jtable({
    title: 'Users list',
    actions: {
      listAction:   Routing.generate('big_elibre_admin', {mode: 'user', action: 'list'}),
      createAction: Routing.generate('big_elibre_admin', {mode: 'user', action: 'create'}),
      updateAction: Routing.generate('big_elibre_admin', {mode: 'user', action: 'update'}),
      deleteAction: Routing.generate('big_elibre_admin', {mode: 'user', action: 'delete'})
    },
    fields: {
      UserId: {
        key: true,
        list: false
      },
      Name: {
        title: 'Username',
//        width: '40%'
      },
      Email: {
        title: 'E-mail',
//        width: '20%'
      },
//      RegDate: {
//        title: 'Reg date',
//        width: '30%',
//        type: 'date',
//        create: false,
//        edit: false
//      },
      isEnabled: {
        title: 'Is Enabled ?',
        width: '2%',
        type: 'checkbox',
        values: { 'false' : 'Disabled', 'true' : 'Enabled' }
//        create: false,
//        edit: false
      },
      Password: {
        title: 'Password',
        type: 'password',
        list: false,
      },
      PasswordRetype: {
        title: 'Retype password',
        type: 'password',
        list: false,
      },
      sendMail: {
        title: 'Notify user about account (de)activation',
        type: 'checkbox',
        values: { 'false' : 'No', 'true' : 'Yes' },
        defaultValue: 'false',
        list: false,
      },
    }
  });
  $('#UsersTableContainer').jtable('load');
});

