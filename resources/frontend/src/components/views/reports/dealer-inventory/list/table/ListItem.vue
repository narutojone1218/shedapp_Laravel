<template>

    <tr>
        <td class="text-center" v-for="(dimension, d_id) in dimensions" :key="d_id">

            <template v-if="dimension.id === 'date_created'">
                {{ filters.moment(item.createdAt, 'YYYY-MM-DD HH:mm:ss', 'MM/DD/YYYY HH:mm:ss') }}
            </template>

            <template v-if="dimension.id === 'location_date'">
                <span v-if="item.lastLocation ">
                    {{ filters.moment(item.lastLocation.createdAt, 'YYYY-MM-DD HH:mm:ss', 'MM/DD/YYYY HH:mm:ss') }}
                </span>
            </template>

            <template v-if="dimension.id === 'building_sn'">
                <a :href="'/buildings/#/' + item.id" target="_blank">
                    {{ item.serialNumber }}
                </a>
            </template>

            <template v-if="dimension.id === 'dealer'">
                <span v-if="item.lastLocation">{{ item.lastLocation.location.name }}</span>
            </template>

            <template v-if="dimension.id === 'retail'">
                <span class="money-cell">
                    {{ item.retail !== undefined ? filters.money(item.retail) : '' }}
                </span>
            </template>

            <template v-if="dimension.id === 'style_id'">
                <span v-if="item.buildingModel && item.buildingModel.style">{{ item.buildingModel.style.name }}</span>
            </template>
        </td>
    </tr>

</template>

<script type="text/babel">
    import baseListItem from 'src/components/views/_base/ListItems/list/ListItem.vue'

    export default {
        extends: baseListItem,
        data() {
            return {
            }
        },
        components: {}
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
</style>