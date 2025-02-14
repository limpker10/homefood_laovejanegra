<template>
  <div class="vuertual-numeric-keyboard bg-light rounded border p-3">
    <v-btn v-for="key in keys"
      :key="key"
      @click="press(key)"
      class="btn btn-lg shadow-none"
      dark
      color="#80b6ff"
    >
    <span style="font-size: 25px;">{{ key }}</span></v-btn>
    <!--<button
      v-for="key in keys"
      :key="key"
      @click="press(key)"
      class="btn btn-lg shadow-none"
      :class="keyTheme"
    >{{ key }}</button>-->
    <v-btn class="btn btn-lg shadow-none" 
      color="error" @click="clear('all')">
      <v-icon
          dark
          color="white"
          right
        >
         mdi-backspace-outline
        </v-icon>
    </v-btn>
    <v-btn class="btn btn-lg shadow-none" color="success" @click="excute()">
      <v-icon
          dark
          right
        >
         mdi-check
        </v-icon>
    </v-btn>
  </div>
</template>

<script>
//import 'bootstrap/dist/css/bootstrap.min.css';
import _ from 'lodash';
export default {
  props: {
    selfValue: {
      type: String
    },
    callback: {
      type: Function
    }
  },
  data() {
    return {
      value: '',
      keys: [1,2,3,4,5,6,7,8,9,0],//[...Array(10).keys()],
      keyTheme: 'btn-keyboard',
      buttonTheme: 'btn-danger',
      buttonSuccess: 'btn-success',
    };
  },
  methods: {
    shuffle() {
      this.keys = _.shuffle(this.keys);
    },
    press(key) {
      this.value = `${this.value}${key}`;
      //this.shuffle();
    },
    clear(type) {
      if (type === 'all') this.value = '';
      else this.value = this.value.substring(0, this.value.length - 1);
    },
    excute(){
      this.$emit('callback')
      /*if (this.callback) {
          this.callback()
        }*/
    }
  },
  watch: {
    value() {
      this.$emit('pressed', this.value);
    },
    selfValue() {
      this.value = this.selfValue;
    },
  },
  created() {
    //this.shuffle();
  },
};
</script>

<style scoped>
.vuertual-numeric-keyboard {
  display: grid;
  grid-template-columns: auto auto auto;
  grid-gap: 10px;
}
.vuertual-numeric-keyboard .btn {
  font-weight: bold;
}
.btn-keyboard {
  background-color: #80b6ff;
  color: #ffffff;
}
.p-3 {
    padding: 1rem!important;
}
</style>