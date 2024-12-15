<template>
  <div v-if="writings && writings.length > 0" class="item-container">
    <span class="item-label">Writing and Hands</span>
    <div class="item-value">
      <template v-for="writing in writings">
        <p>
          <span class="block">
            <template v-if="writing.locus && writing.locus !== ''">{{ writing.locus }}:</template>
            {{ writing.script.map(script => script.label).join(', ') }}
          </span>
          <template v-if="writing.note && writing.note.length > 0">
            <span v-for="note in writing.note" class="block">
              {{ note }}
            </span>
          </template>
        </p>
      </template>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    writings: {type: Array, required: false, default: []},
  },
};
</script>