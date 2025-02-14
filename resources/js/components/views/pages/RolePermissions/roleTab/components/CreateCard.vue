<template>
  <div>
     <v-card v-if="!loading">
      <v-card-title>Añadir Rol</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="5" sm="6">
            <v-text-field
              v-model="$title"
              label="Nombre"
              placeholder="Nombre del Rol"
              :rules="rules"
            >
            </v-text-field>
          </v-col>
          <v-col cols="12" md="5" sm="6">
            <v-text-field
              v-model="$name"
              label="Identificador"
              placeholder="student"
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
                Crear Rol
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
  name: "create-rol-card",
  props: {
    title: {
      type: String,
      default: "",
    },
    name: {
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
    $title: {
      get() {
        return this.title;
      },
      set(value) {
        this.set_prop("title", value);
      },
    },
    $name: {
      get() {
        return this.name;
      },
      set(value) {
        this.set_prop("name", value);
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

