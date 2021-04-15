<template>
  <div>
    <h1 class="text-center">Créateur de formulaire</h1>
    <div class="container-sm shadow-lg p-4">
      <transition name="fade" mode="out-in" >
        <input ref="title-input"  v-if="title.isSelected" @focusout="title.isSelected = false" @transitionend="focusOnTitle"   type="text" class="form-control mb-2" placeholder="Le titre du Formulaire" v-model="title.value">
        <h2  ref="title" v-else class="text-muted" @click="title.isSelected = true">
          <span v-if="title.value.length > 0">{{title.value}}</span>
          <span v-else>Le titre du formulaire</span>
        </h2>
      </transition>

      <div>
      <button class="btn btn-primary rounded-0 shadow" @click="addField">Ajouter un champs</button>
        <div v-if="fields.length >0">
          <transition-group tag="ul" name="scale">

          <question :ref="field.ref"   v-for="(field, index) in fields" :key="field.ref" :removable="isFirst(index)" :numero="index"  @Remove="removeQuestion" ></question>
          </transition-group>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import Question from "./Question";
  export default{
    components:{
      Question
    },
    name: 'form-builder',
    data(){
      return {
        show:true,
        // correspond a la parties titre du form builder
        title:{
          isSelected:false, // détermine si le titre et selectionner pour etre modifié
          value: ''
        },
        fields: [{ref:this.generateUid(32)}] // va contenir les différents questions

      }
    },
    methods: {
      generateUid(len){
        let uid = ''
        let alphabet= 'abcdefghijklmnopqrstuvwxyz'
        for (let i = 0; i < len; i++) {
          let letter = alphabet.substr(Math.floor(Math.random()* (alphabet.length - 1)),1)
          if ((Math.floor(Math.random()* (alphabet.length - 1)))%2 === 0){
            letter = letter.toUpperCase()
          }
          uid += letter
        }
        return uid
      },
      addField(){
          let field = {ref: this.generateUid(32)}
          this.fields.push(field)
      },
      removeQuestion(n){
        this.fields.splice(n,1)
      },
      isFirst(n) {
        return n !== 0  ;

      },
      focusOnTitle(){
        if (this.$refs["title-input"]){
          this.$refs['title-input'].focus()
        }
      }
    },
    computed:{
      isHere(){
        if (this.$refs["title-input"]){
          this.$refs["title-input"].focus()
          return true
        }else
          return false
      }
    },

  }
</script>
<style>

.fade-enter-active, .fade-leave-active {
  transition: opacity .3s ease;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
.scale-enter-active {
  -webkit-animation: scale-in-ver-top 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
  animation: scale-in-ver-top 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
}
.scale-leave-active {
  -webkit-animation: scale-out-ver-top 0.5s cubic-bezier(0.550, 0.085, 0.680, 0.530) both;
  animation: scale-out-ver-top 0.5s cubic-bezier(0.550, 0.085, 0.680, 0.530) both;
}





@-webkit-keyframes scale-in-ver-top {
  0% {
    -webkit-transform: scaleY(0);
    transform: scaleY(0);
    -webkit-transform-origin: 100% 0%;
    transform-origin: 100% 0%;
    opacity: 1;
  }
  100% {
    -webkit-transform: scaleY(1);
    transform: scaleY(1);
    -webkit-transform-origin: 100% 0%;
    transform-origin: 100% 0%;
    opacity: 1;
  }
}
@keyframes scale-in-ver-top {
  0% {
    -webkit-transform: scaleY(0);
    transform: scaleY(0);
    -webkit-transform-origin: 100% 0%;
    transform-origin: 100% 0%;
    opacity: 1;
  }
  100% {
    -webkit-transform: scaleY(1);
    transform: scaleY(1);
    -webkit-transform-origin: 100% 0%;
    transform-origin: 100% 0%;
    opacity: 1;
  }
}


@-webkit-keyframes scale-out-ver-top {
  0% {
    -webkit-transform: scaleY(1);
    transform: scaleY(1);
    -webkit-transform-origin: 100% 0%;
    transform-origin: 100% 0%;
    opacity: 1;
  }
  100% {
    -webkit-transform: scaleY(0);
    transform: scaleY(0);
    -webkit-transform-origin: 100% 0%;
    transform-origin: 100% 0%;
    opacity: 1;
  }
}
@keyframes scale-out-ver-top {
  0% {
    -webkit-transform: scaleY(1);
    transform: scaleY(1);
    -webkit-transform-origin: 100% 0%;
    transform-origin: 100% 0%;
    opacity: 1;
  }
  100% {
    -webkit-transform: scaleY(0);
    transform: scaleY(0);
    -webkit-transform-origin: 100% 0%;
    transform-origin: 100% 0%;
    opacity: 1;
  }
}



</style>
