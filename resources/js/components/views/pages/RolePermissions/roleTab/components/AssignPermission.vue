<template>
  <div>
    <v-card v-if="!loading">
        <v-card-title>Asignar permisos a un rol</v-card-title>
            <v-card-text>
                <div class="d-flex flex-column flex-sm-row">
                <div class="flex-grow-1 pt-2 pa-sm-2" style="padding: 0px 16px !important;">
                    <v-row>
                        <v-col cols="12" md="4" style="width: 100%; padding: 0px 24px;">
                           <v-select v-model="$role_selected" label="Rol de usuario" placeholder="Selecciona un rol" required :items="role_list" item-text="title" item-value="id" ></v-select>
                        </v-col>
                    </v-row>
                    <v-data-table
                        :headers="headers"
                        :items="permissions_list"    
                        class="flex-grow-1 scroll-me"
                        :loading="loading"
                        v-if="role_selected!=null"
                        >
                        
                        <template v-slot:[`item.action`]="{ item }">
                            <div class="actions d-flex justify-content-between">
                                <v-switch
                                :value="item.id"
                                v-model="$switch_model"
                                ></v-switch>
                            </div>
                        </template>
                    </v-data-table>
                    <div class="mt-2">
                        <v-btn  class="btn-actions" @click="clearpermissions">Cancelar</v-btn>
                        <v-btn color="primary" @click="validate" :loading="loading" :disabled="!role_selected">Guardar cambios</v-btn>
                    </div>
                </div>
            </div>
            </v-card-text>
    </v-card>
    
    <template v-else>
      <v-skeleton-loader class="mx-auto" type="image"></v-skeleton-loader>
    </template>
  </div>
</template>

<script>
export default {
  name: "assign-permission-card",
  props: {
    role_list: {
      type: Array,
      default: [],
    },
    switch_model: {
      type: Array,
      default: [],
    },
    permissions_list: {
      type: Array,
      default: [],
    },
    headers: {
      type: Array,
      default: [],
    },
    loading: {
      type: Boolean,
      default: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    validate:{
        type:Function,
        default:()=>{}
    },
    role_selected: {
      type: Number,
      default: 0,
    },
  },
  computed: {
    $role_list: {
      get() {
        return this.role_list;
      },
      set(value) {
        this.set_prop("role_list", value);
      },
    },
    $role_selected: {
      get() {
        return this.role_selected;
      },
      set(value) {
        this.set_prop("role_selected", value);
      },
    },
    $switch_model: {
      get() {
        return this.switch_model;
      },
      set(value) {
        this.set_prop("switch_model", value);
      },
    },
  },
  components: {},
  data() {
    return {
      rules: [(v) => !!v || "Campo es obligatorio"],
      //switch_model: [],
      //role_selected: null
    };
  },
  created() {},
  watch: {
        role_selected(){
            this.getrolepermissions();
        }
  },
  methods: {
    set_prop(_prop, value) {
      this.$emit("update:" + _prop, value);
    },
    async getrolepermissions(){
        try{
            const rolepermissions = await API.permissions.read(this.role_selected);
            //this.switch_model = rolepermissions.data.map(({ id }) => id);
            this.set_prop("switch_model", rolepermissions.data.map(({ id }) => id));
        }catch(error){
            console.error(error.response.status);
        }
    },
    
    clearpermissions(){
        this.switch_model = [];
        this.role_selected = null;
    },
  },
};
</script>

<style lang="scss" scoped>
.mt-2{
    text-align: right;
  }
.btn-actions {
  background-color: #fff !important;
  color: #121212;
}
</style>