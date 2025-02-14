const ACL = {
    permissions:{
        can(permission) {
            const permissions = JSON.parse(localStorage.getItem('user_permissions'));
            return permissions.includes(permission);
            //return true;
        },
        cangroup(menu_permissions){
            const permissions = JSON.parse(localStorage.getItem('user_permissions'));
            for(var i = 0; i < menu_permissions.length; i++){
                const element = menu_permissions[i][3];
                if(permissions.includes(element)) return true;
            }
            return false;
            //return true;
        }
    },
    role:{
        can(role){
            const roles = JSON.parse(localStorage.getItem('user_roles'));
            return roles.includes(role);
        }
    }
}
export default ACL;