<template>
  <div class="border border-light p-3 mt-3 bg-light shadow-sm">
    <h3>Question {{numero+1}}</h3>
    <transition name="fade" mode="out-in">
      <h4 class="text-muted editable"  v-if="!title.isSelected" @click="title.isSelected = true">
        <span v-if="title.value.length > 0">{{title.value}}</span>
        <span v-else>Titre de la question</span>
      </h4>
      <input ref="title-input" type="text" v-else class="form-control" @transitionend="focusOnTitle" @focusout="title.isSelected = false" @keyup.enter="title.isSelected = false" v-model="title.value" placeholder="Titre de la question">
    </transition>

    <label>Type de question</label>
    <select class="form-control" v-model="selectedOption">
      <option :value="null" disabled>Sélectionnez un type de question</option>
      <option v-for="index in options.length" :key="index">
        {{options[index-1]}}
      </option>
    </select>
    <div class="text-right mt-2" v-if="removable">
      <button class="btn btn-outline-danger rounded-0" @click="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
</template>

<script>
export default {
  name: "question",
  data(){
    return{
      options: [],
      selectedOption: null,
      title:{
        isSelected:false, // détermine si le titre et selectionner pour etre modifié
        value: ''
      },
    }
  },
  props:{
    numero: null,
    removable:{
      type: Boolean,
      default: true
    }
  },
  methods: {
    focusOnTitle() {
      if (this.$refs["title-input"]){
        this.$refs["title-input"].focus()
      }
    },
    remove(){
      this.$emit('Remove', this.numero)
    }
  },
  mounted() {
    this.options = [
        'text',
        'multiple',
        'radio'
    ]
  }
}
</script>

<style scoped>
.editable{
  cursor: pointer;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity .3s ease;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>