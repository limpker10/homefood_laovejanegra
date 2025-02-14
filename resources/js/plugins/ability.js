import { defineAbility } from '@casl/ability';
import axios from 'axios';

export default defineAbility((can, cannot) => {
  can('dashboard', 'all');
  can('blank', 'all');
  can('error_not_found','all');
  let rules = [];
  let roles = [];


  /*axios.get('/api/user').then(response => {
    const currentUser = response.data;
    for (let index = 0; index < currentUser.permissions.length; index++) {
      const element = currentUser.permissions[index];
      can(element, 'all');
    }
  }).catch(error => {
  });*/
  /*if(localStorage.getItem('user_permissions')!='undefined' && localStorage.getItem('user_roles')!='undefined' ){
    rules = JSON.parse(localStorage.getItem('user_permissions'));
    roles = JSON.parse(localStorage.getItem('user_roles'))
    if(rules){
      for (let index = 0; index < rules.length; index++) {
        const element = rules[index];
        can(element, 'all');
      }
    }
    if(roles){
      let found = roles.findIndex((element) => element == 'admin');
      if(found > -1){
        can('read_roles', 'all');
      }
    }
  }*/
  
 
});
