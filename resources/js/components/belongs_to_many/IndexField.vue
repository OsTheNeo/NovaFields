<template>
    <div class="max-w-xxs overflow-y-auto py-2">
        <span v-for="(resource, key) in field.value" class="single">
            <Link v-if="field.viewable" :href="$url(`/resources/${field.resourceNameRelationship}/${resource.id}`)" class="link-default no-underline font-bold dim mr-1">
                    {{ get(resource, field.optionsLabel) + (key < field.value.length - 1 ? ', ' : '') }} </Link>
            <span v-else>{{ get(resource, field.optionsLabel) }}</span>
        </span>
    </div>
</template>

<script>
import get from "lodash.get";

export default {
    name: "BelongsToManyIndexField",
    props: ['resourceName', 'field'],
    methods: {
        get(object, path, defaultValue) {
            return get(object, path, defaultValue);
        },
    }
}
</script>
