<template v-if="paracontents && paracontents.length > 0">
  <div v-for="para in paracontents" class="mb-8">
    <p>
      <strong>
        <template v-if="partLabel">{{ partLabel }}, </template>{{ para.locus }}, {{ para.label }}<template v-if="showParaTypeLabel"> ({{ para.type.label }})</template>
      </strong>
    </p>

    <p v-if="(para.lang && para.lang.length > 0) || (para.script && para.script.length > 0)" class="indent">
      <template v-if="para.lang && para.lang.length > 0">
        Languages: {{ para.lang.map(lang => lang.label).join(', ') }}
      </template>
      <template v-if="para.lang && para.lang.length > 0 && para.script && para.script.length > 0">
        |
      </template>
      <template v-if="para.script && para.script.length > 0">
        Scripts: {{ para.script.map(script => script.label).join(', ') }}
      </template>
    </p>

    <p v-if="para.as_written" class="indent">
      Transcription: {{ para.as_written }}
    </p>

    <template v-if="para.translation && para.translation.length > 0">
      <p v-for="translation in para.translation" class="indent">
        Translation: {{ translation }}
      </p>
    </template>

    <p v-if="(para.assoc_name && para.assoc_name.length > 0) || (para.assoc_place && para.assoc_place.length > 0) || (para.assoc_date && para.assoc_date.length > 0)" class="indent font-medium italic">
      Associated Names, Places, Dates
    </p>

    <template v-if="para.assoc_name && para.assoc_name.length > 0">
      <ul class="indent">
        <li v-for="name in para.assoc_name" class="indent">
          {{ name.role.label }}: {{ [name.as_written, name.pref_name].filter(Boolean).join(' | ') }}
          <span v-if="name.note && name.note.length > 0" class="indent">
            {{ name.note.join('; ') }}
          </span>
        </li>
      </ul>
    </template>

    <div v-if="para.assoc_place && para.assoc_place.length > 0" class="mt-2">
      <ul class="indent">
        <li v-for="(place, placeIndex) in para.assoc_place" :key="placeIndex" class="indent">
          {{ place.event.label }}: {{ [place.as_written, place.pref_name].filter(Boolean).join(' | ') }}
          <span v-if="place.note && place.note.length > 0" class="indent">
            {{ place.note.join('; ') }}
          </span>
        </li>
      </ul>
    </div>

    <div v-if="para.assoc_date && para.assoc_date.length > 0" class="mt-2">
      <ul class="indent">
        <li v-for="(date, dateIndex) in para.assoc_date" :key="dateIndex" class="indent">
          {{ date.type.label }}: {{ [date.as_written, date.value].filter(Boolean).join(' | ') }}
          <span v-if="date.note && date.note.length > 0" class="indent">
            {{ date.note.join('; ') }}
          </span>
        </li>
      </ul>
    </div>

    <template v-if="para.note && para.note.length > 0">
      <div class="indent">
        <p class="font-medium italic">
          Notes
        </p>
        <p v-for="note in para.note" class="indent">
          {{ note }}
        </p>
      </div>
    </template>
  </div>
</template>
<script>
export default {
  props: {
    paracontents: { type: Array, required: true },
    showParaTypeLabel: { type: Boolean, default: false },
    partLabel: { type: String, default: null },
  },
};
</script>
