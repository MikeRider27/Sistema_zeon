<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    initialTables: { type: Array, default: () => [] },
    dataUrl: { type: String, required: true },
    orderBaseUrl: { type: String, required: true },
    canOrder: { type: Boolean, default: false },
});

const tables = ref(props.initialTables);
let timer = null;

async function refresh() {
    try {
        const { data } = await axios.get(props.dataUrl);
        tables.value = data;
    } catch (e) {
        // silently ignore transient polling failures
    }
}

onMounted(() => {
    timer = setInterval(refresh, 6000);
});

onUnmounted(() => {
    clearInterval(timer);
});

function statusClasses(table) {
    return table.status === 'libre'
        ? 'bg-green-50 border-green-300 text-green-800'
        : 'bg-amber-50 border-amber-300 text-amber-900';
}

function href(table) {
    return `${props.orderBaseUrl}/${table.id}/pedido`;
}
</script>

<template>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        <component
            v-for="table in tables"
            :key="table.id"
            :is="canOrder ? 'a' : 'div'"
            :href="canOrder ? href(table) : undefined"
            class="block border rounded-lg p-4 shadow-sm hover:shadow-md transition"
            :class="statusClasses(table)"
        >
            <div class="flex items-center justify-between">
                <span class="text-lg font-semibold">Mesa {{ table.number }}</span>
                <span class="text-xs uppercase tracking-wide">{{ table.status }}</span>
            </div>
            <p class="text-sm mt-1 opacity-75">Capacidad: {{ table.capacity }}</p>
            <p v-if="table.current_order" class="text-sm mt-2 font-medium">
                Total: ${{ table.current_order.total.toFixed(2) }}
            </p>
        </component>
    </div>
</template>
