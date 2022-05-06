<template>
    <PanelItem :field="field">
        <template #value>
              <span v-for="(resource, key) in field.value" class="single">
                  <Link v-if="field.viewable" :href="$url(`/resources/${field.resourceNameRelationship}/${resource.id}`)" class="link-default no-underline font-bold dim mr-2 ">
                    {{ get(resource, field.optionsLabel) }}
                      {{ key < field.value.length - 1 ? ', ' : '' }}
                  </Link>
                <span v-else>{{ get(resource, field.optionsLabel) }}</span>
              </span>
        </template>
    </PanelItem>
</template>

<script>
import get from 'lodash.get'

export default {
    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
    methods: {
        get(object, path, defaultValue) {
            return get(object, path, defaultValue);
        }
    }
};
</script>
