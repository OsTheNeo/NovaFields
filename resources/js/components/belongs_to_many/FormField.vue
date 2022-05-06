<template>
    <DefaultField :field="field" :errors="errors" :show-help-text="true">
        <template #field>
            <div :style="{ height: field.height ? field.height : 'auto' }" class="relative">

                <Multiselect
                    ref="multiselect"
                    v-model="value"
                    :searchable="true"
                    mode="tags"
                    v-bind="multiSelectProps"
                    :options="options">
                    <template slot="noOptions">{{ field.multiselectSlots.noOptions }}</template>
                    <template slot="noResult">{{ field.multiselectSlots.noResult }}</template>
                </Multiselect>
                       <label v-if="this.field.selectAll"><input type="checkbox" class="checkbox mb-2 mr-2">{{this.field.messageSelectAll}}</label>

            </div>
        </template>
    </DefaultField>
</template>

<script>
import {FormField, HandlesValidationErrors} from "laravel-nova";
import Multiselect from '@vueform/multiselect'
import get from "lodash.get";

export default {
    mixins: [FormField, HandlesValidationErrors],
    props: ["resourceName", "resourceId", "field"],
    components: {Multiselect},
    data() {
        return {
            options: [],
            optionsLabel: "name",
            dependsOnValue: null,
            isDependant: false,
            shouldClear: false,
            loading: true,
            selectAll: false,
        };
    },
    mounted() {
        this.field.visible = true;
    },
    created() {
        if (this.field.dependsOn !== undefined) {
            this.isDependant = true;
            this.registerDependencyWatchers(this.$root);
        }

    },

    computed: {
        multiSelectProps() {
            return {
                multiple: true,
                customLabel: (el) => get(el, this.optionsLabel),
                trackBy: this.trackBy,
                preselectFirst: false,
                class: this.errorClasses,
                placeholder: this.field.name,
                ...(this.field.multiselectOptions ? this.field.multiselectOptions : {}),
            };
        },
    },
    watch: {
        selectAll(value) {
            if (value) {
                this.value = [...this.options];
            } else {
                this.value = [];
            }
        },
    },
    methods: {

        registerDependencyWatchers(root) {
            console.log('registerDependencyWatchers')
            root.$children.forEach((component) => {
                if (this.componentIsDependency(component)) {
                    if (component.selectedResourceId !== undefined) {
                        let attribute = this.findWatchableComponentAttribute(component);
                        component.$watch(attribute, this.dependencyWatcher, {
                            immediate: true,
                        });
                        this.dependencyWatcher(component.selectedResourceId);
                    }
                }
                this.registerDependencyWatchers(component);
            });
        },

        findWatchableComponentAttribute(component) {
            let attribute;
            if (component.field.component === "belongs-to-field") {
                attribute = "selectedResource";
            } else {
                attribute = "value";
            }
            return attribute;
        },

        componentIsDependency(component) {
            if (component.field === undefined) {
                return false;
            }
            return component.field.attribute === this.field.dependsOn;
        },

        dependencyWatcher(value) {
            if (value === this.dependsOnValue) {
                return;
            }
            this.dependsOnValue = value.value;
            this.fetchOptions();
        },

        setInitialValue() {
            this.optionsLabel = this.field.optionsLabel ? this.field.optionsLabel : "name";
            this.value = this.field.value.map((item) => {
                return item.id;
            });
            this.fetchOptions();
        },

        fetchOptions() {
            if (this.field.options) {
                this.options = this.field.options;
                this.loading = false;
                return;
            }
            let baseUrl = "/nova-vendor/belongs-to-many-field/";
            if (this.isDependant) {
                if (this.dependsOnValue) {
                    this.loading = true;
                    Nova.request(baseUrl + this.resourceName + "/" + "options/" + this.field.attribute + "/" + this.optionsLabel + "/" + this.dependsOnValue + "/" + this.field.dependsOnKey)
                        .then((data) => {
                            this.options = data.data;
                            this.loading = false;
                        });
                } else {
                    this.options = [];
                    this.loading = false;
                }
            } else {
                Nova.request(baseUrl + this.resourceName + "/" + "options/" + this.field.attribute + "/" + this.optionsLabel)
                    .then((data) => {
                        this.options = data.data;
                        this.loading = false;
                    });
            }
        },


        fill(formData) {
            let data = [];

            this.value.forEach((el) => {
                let option = this.options.find((option) => option.id === el);
                if (option) data.push(option);
            });

            formData.append(this.field.attribute, JSON.stringify(data) || "");
        },

        handleChange(value) {
            this.value = value;
            this.$nextTick(() => this.repositionDropdown());
        },
    },
};
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
<style type="text/css">
.multiselect__placeholder {
    font-size: 1rem;
    color: var(--70) !important;
    margin-left: 4px;
}

.multiselect__tags {
    border-width: 1px;
    border-color: var(--60);
}

.multiselect__select {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 35px;
}

.multiselect__select::before {
    border-width: 0; /* Reset default style */

    /*position: absolute;*/
    top: 0;
    width: 22px;
    height: 6px;
    margin: 0;

    background-repeat: no-repeat;
    background-position: center center;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 6"><path fill="%2335393C" fill-rule="nonzero" d="M8.293.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4A1 1 0 0 1 1.707.293L5 3.586 8.293.293z"/></svg>');
}
</style>


