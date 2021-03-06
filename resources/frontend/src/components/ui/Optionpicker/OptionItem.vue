<template>

    <tr>

            <td>
                {{ buildingOption.option.name }}
                <colorpicker v-if="buildingOption.option.allowableColors && buildingOption.option.allowableColors.length > 0"
                             ref="colorPicker"
                             v-on:select-color="selectColor"
                             v-on:update-color="updateColor"
                             v-bind:validation="validation"
                             v-bind:type="buildingOption.id"
                             v-bind:colors="buildingOption.option.allowableColors"
                             v-bind:color="buildingOption.color">
                </colorpicker>
            </td>
            <td class="nowrap">
                <div v-if="editable" class="btn-group">
                    <div class="form-group">
                        <div class="input-group input-group-xs" style="width: 75px">
                            <span class="input-group-addon">$</span>
                            <input number
                                   type="number"
                                   class="form-control"
                                   initial="off"
                                   v-on:input="updateOption(buildingOption, { unitPrice: parseFloat($event.target.value) })"
                                   v-bind:value="buildingOption.unitPrice"/>
                        </div>
                    </div>
                </div>
                <span v-else>{{ filters.money(buildingOption.unitPrice) }}</span>
            </td>

            <td class="text-left">
                <div class="btn-group">
                    <div class="form-group">
                        <div class="input-group input-group-xs" style="width: 100px">
                            <div class="input-group-btn btn-group-xs" role="group">
                                <button type="button" class="btn btn-default" v-on:click="increaseOption(buildingOption)">
                                    <i class="fa fa-plus fa-fw"></i>
                                </button>
                            </div>
                            <input number
                                   type="number"
                                   class="form-control"
                                   initial="off"
                                   style="width: 50px"
                                   v-bind:min="buildingOption.minQuantity || null"
                                   v-on:input="updateOption(buildingOption, { quantity: parseInt($event.target.value) })"
                                   v-bind:value="buildingOption.quantity"/>
                            <div class="input-group-btn btn-group-xs" role="group">
                                <button type="button" class="btn btn-default"
                                        v-on:click="decreaseOption(buildingOption)"
                                        v-bind:disabled="!canDecrease">
                                    <i class="fa fa-minus fa-fw"></i>
                                </button>
                                <button type="button" class="btn btn-default"
                                        v-on:click="removeOption(buildingOption)"
                                        v-bind:disabled="!canRemove">
                                    <i class="fa fa-times fa-fw"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <span v-if="buildingOption.minQuantity" class="label label-default nowrap">Min. quantity: {{ buildingOption.minQuantity }}</span>
            </td>
            <td class="text-right nowrap">{{ buildingOption.unitPrice * buildingOption.quantity | money }}</td>

    </tr>

</template>

<script type="text/babel">
    import Colorpicker from 'src/components/ui/Colorpicker/ColorpickerSlim.vue'
    import Popover from 'vue-strap/src/Popover.vue'

    export default {
        components: {
            Colorpicker,
            Popover
        },
        data() {
            return {}
        },
        props: {
            editable: {
                default: false
            },
            validation: {
                default() {
                    return {}
                }
            },
            buildingOption: {
                default() { return {} }
            },
            buildingModel: {
                default() { return {} }
            }
        },
        created() {
            if (!_.isUndefined(this.color)) {
                this.$watch('color', this.onChangeColor, {
                    deep: true
                })
            }
        },
        computed: {
            color() {
                return this.buildingOption.color
            },
            bo() {
                return this.buildingOption
            },
            canDecrease() {
                if (this.bo.quantity <= this.bo.minQuantity) {
                    return false
                }
                if (_.isArray(this.bo.parentOptions) && this.bo.parentOptions.length >= this.bo.quantity) {
                    return false
                }
                return true
            },
            canRemove() {
                if (_.isArray(this.bo.parentOptions)) {
                    return (this.bo.parentOptions.length === 0)
                }
                return true
            }
        },
        methods: {
            validate() {
                if (this.$refs.colorPicker) {
                    this.$refs.colorPicker.$v.$touch()
                    return !this.$refs.colorPicker.$v.$error
                }
                return true
            },
            onChangeColor(newColor, oldColor) {
                // don't touch anything if colors are the same (fix for updating name of color)
                if (_.isObject(newColor) && _.isObject(oldColor) && newColor.id === oldColor.id) return

                // add option related to new color
                if (_.isObject(newColor) && newColor['optionId']) {
                    let buildingOption = this.$parent.getBuildingOptionByOptionId(newColor['optionId'])
                    let colorOption = this.$parent.getOptionByOptionId(newColor['optionId'])
                    if (!colorOption) return

                    this.$parent.$emit('option-manager-increase-children-options', this.buildingOption, [buildingOption], colorOption)
                }

                // remove option related to old color
                if (_.isObject(oldColor) && oldColor['optionId']) {
                    let buildingOption = this.$parent.getBuildingOptionByOptionId(oldColor['optionId'])
                    if (buildingOption) {
                        this.$parent.$emit('option-manager-decrease-children-options', this.buildingOption, [buildingOption])
                    }
                }
            },
            addOption(option, extraProps) {
                this.$parent.$emit('option-manager-add-option', option, extraProps)
            },
            updateOption(buildingOption, params) {
                this.$parent.$emit('option-manager-update-option', buildingOption, params)
            },
            updateColor (color) {
                this.$emit('option-item-update-color', this.buildingOption, { color: color })
            },
            selectColor(color) {
                this.$emit('option-item-select-color', this.buildingOption, { color: color })
            },
            removeOption(buildingOption) {
                this.$parent.$emit('option-manager-remove-option', buildingOption)
            },
            increaseOption(buildingOption) {
                this.$parent.$emit('option-manager-increase-option', buildingOption)
            },
            decreaseOption(buildingOption) {
                this.$parent.$emit('option-manager-decrease-option', buildingOption)
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    .nowrap {
        white-space: nowrap;
    }
</style>

<style type="text/css">
    .option-picker__button-countainer {
        position: relative;
        white-space: nowrap;
    }
</style>