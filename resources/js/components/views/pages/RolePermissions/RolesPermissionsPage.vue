<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Roles y Permisos</h2>
      </div>
      <v-spacer></v-spacer>
    </div>
    
    <v-tabs v-model="tab" :show-arrows="false" background-color="transparent">
      <v-tab to="#tabs-role">Roles</v-tab>
      <v-tab to="#tabs-permission">Permisos</v-tab>
    </v-tabs>

    <v-tabs-items v-model="tab"  background-color="transparent">
      <v-tab-item value="tabs-role">
          <role-tab ref="tabs-role"></role-tab>
      </v-tab-item>

      <v-tab-item value="tabs-permission">
          <permission-tab ref="tabs-permission"></permission-tab>
      </v-tab-item>
    </v-tabs-items>
    
    <!--<template v-else>
      <v-skeleton-loader
        class="mx-auto"
        type="image,divider,image"
      ></v-skeleton-loader>
    </template>-->
  </div>
</template>

<script>
import RoleTab from './roleTab/RoleTab'
import PermissionTab from './permissionTab/PermissionTab'

export default {
  components: {
    RoleTab,
    PermissionTab
  },
  data() {
    return {
      tab: null,
      breadcrumbs: [
        {
          text: 'Roles',
          to: '/role',
          exact: true
        }
      ]
    }
  },
  created() {
  },
  methods: {
  }
}
</script>
<style lang="scss" scoped>
  .btn-actions{
    background-color: #fff !important;
    color: #121212;
  }
  .theme--light.v-tabs-items {
    background-color: transparent !important;
}
</style>
