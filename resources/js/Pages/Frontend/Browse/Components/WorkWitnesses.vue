<template>
  <template v-if="workWitnesses && workWitnesses.length > 0">
    <div v-for="workWit in workWitnesses" class="mb-8">
      <p>
        <strong>{{ workWit.title }}; {{ workWit.locus }}</strong>
      </p>

      <div class="indent">

        <p v-for="creator in workWit.work.creator">
          {{ creator.role.label }}: {{ creator.pref_name }}
        </p>

        <p v-if="workWit.work.alt_title && workWit.work.alt_title.length > 0">
          Alternate title: {{ workWit.work.alt_title.join(', ') }}
        </p>

        <p v-if="workWit.as_written && workWit.as_written !== ''">
          Transcribed Title: {{ workWit.as_written }}
        </p>

        <template v-if="workWit.excerpt && workWit.excerpt.length > 0">
          <p>
            Excerpts:
          </p>
          <div class="indent">
            <template v-for="excerpt in workWit.excerpt">
              <p>
                {{ excerpt.type.label }}, {{ excerpt.locus }}
              </p>
              <div class="indent">
                <p v-if="excerpt.as_written && excerpt.as_written !== ''">
                  Transcription: {{ excerpt.as_written }}
                </p>
                <p v-if="excerpt.translation && excerpt.translation !== ''">
                  Translation: {{ excerpt.translation }}
                </p>
                <p v-if="excerpt.note && excerpt.note.length > 0">
                  Note: {{ excerpt.note.join('; ') }}
                </p>
              </div>
            </template>
          </div>
        </template>

        <template v-if="workWit.note && workWit.note.length > 0">
          <p>
            Witness Notes:
          </p>
          <p v-for="note in workWit.note" class="indent">
            {{ note }}
          </p>
        </template>

      </div>

    </div>
  </template>
</template>
<script>
export default {
  props: {
    workWitnesses: {type: Array, required: true},
  },
};
</script>