import Vuex from 'vuex'
import Vue from 'vue'
Vue.use(Vuex)


const saveState = (state) => {
  try {
    const serializedState = JSON.stringify(state);
    localStorage.setItem('mesas', serializedState);
  } catch (err) {
    console.error(`Something went wrong: ${err}`);
  }
}

const loadMesas = () => {
  try {
    const serializedState = localStorage.getItem('mesas');
    if (serializedState === null) {
      return undefined;
    }
    return JSON.parse(serializedState);
  } catch (err) {
    return undefined;
  }
};

export default new Vuex.Store({

  state: {
    contacts: [],
    token: localStorage.getItem('token') || '',
    status:'',
    loader:false,
    mode: 'hotel',
    mesas: loadMesas() || [],
  },

  mutations:{
      authSuccess(state,token){
          state.token=token;
          state.status='success';
      },
      LOADER(state,payload){
        state.loader = payload;
      },
      mode(state,payload){
        state.mode = payload;
      },
      authError(state){
          state.token='';
          state.status='error';
      },
      authLogout(state){
        state.token=''
      },


      toggleEditing(state, action) {
        state.mesas.map((t, i) => {
          if (action.todo.id === t.id) {
            state.mesas[i] = action.todo;
          }
        });
        saveState(state.mesas);
      },
  },

  actions: {
      login(context, payload) {

        return new Promise((resolve,reject)=>{

          axios.post('/login', payload)
            .then((response) => {
              let accessToken = response.data.auth.access_token;
              context.commit('authSuccess', accessToken)
              localStorage.setItem('token', accessToken);
              axios.defaults.headers.common['Authorization'] = "Bearer " + accessToken;

              resolve(response);

            })
            .catch((error) => {
              localStorage.removeItem('token');
              context.commit('authError')
              console.log(error);
              reject(error);
            })

        })
         
      },
     
      register(context, payload) {

        return new Promise((resolve,reject)=>{

          axios.post('/register', payload)
            .then((response) => {
              let accessToken = response.data.auth.access_token;
              context.commit('authSuccess', accessToken)
              localStorage.setItem('token', accessToken);
              axios.defaults.headers.common['Authorization'] = "Bearer " + accessToken;

              resolve(response);

            })
            .catch((error) => {
              localStorage.removeItem('token');
              context.commit('authError')
              console.log(error);
              reject(error);
            })

        })
         
      },

      logout(context){
        return new Promise((resolve,reject)=>{
            context.commit('authLogout')
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'] ;

            resolve();


        })
      }

  },


  getters: {
    isAuthenticated: state => !!state.token,
    authStatus: state => state.status,
    menus:(state,getters)=>{
      if(getters.isAuthenticated){
        return [{
          name: "Logout", route: "Logout"
        }]
      }
      return [
        { name: "Signup", route: "Signup" },
        { name: "Login", route: "Login" },
      ];
    }
  }

});