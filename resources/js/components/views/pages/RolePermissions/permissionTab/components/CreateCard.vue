<template>
  <div>
     <v-card v-if="!loading">
      <v-card-title>Añadir Permiso</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="4" sm="6">
            <v-text-field
              v-model="$name"
              label="Identificador"
              placeholder="read_users, create_items"
              :rules="rules"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="6" sm="6">
            <v-text-field
              v-model="$description"
              label="Descripción"
              placeholder="El usuario puede..."
              :rules="rules"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="2" sm="6">
            <v-btn
                color="primary"
                @click="validate"
                :disabled="disabled"
                :loading="loading"
                ><v-icon>mdi-plus</v-icon>
                Crear Permiso
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
    
    <template v-else>
      <v-skeleton-loader class="mx-auto" type="image"></v-skeleton-loader>
    </template>
  </div>
</template>

<script>
export default {
  name: "create-permission-card",
  props: {
    name: {
      type: String,
      default: "",
    },
    description: {
      type: String,
      default: "",
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
    }
  },
  computed: {
    $name: {
      get() {
        return this.name;
      },
      set(value) {
        this.set_prop("name", value);
      },
    },
    $description: {
      get() {
        return this.description;
      },
      set(value) {
        this.set_prop("description", value);
      },
    },
  },
  components: {},
  data() {
    return {
      rules: [(v) => !!v || "Campo es obligatorio"],
    };
  },
  created() {},
  methods: {
    set_prop(_prop, value) {
      this.$emit("update:" + _prop, value);
    },
  },
};
</script>

