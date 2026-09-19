<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    initialTickets: { type: Array, default: () => [] },
    dataUrl: { type: String, required: true },
    updateUrlBase: { type: String, required: true },
});

const tickets = ref(props.initialTickets);
let timer = null;

async function refresh() {
    try {
        const { data } = await axios.get(props.dataUrl);
        tickets.value = data;
    } catch (e) {
        // ignore transient polling errors
    }
}

onMounted(() => {
    timer = setInterval(refresh, 4000);
});

onUnmounted(() => clearInterval(timer));

async function advance(ticket) {
    const nextStatus = ticket.status === 'pendiente' ? 'preparando' : 'listo';

    try {
        const { data } = await axios.patch(`${props.updateUrlBase}/${ticket.id}`, { status: nextStatus });
        tickets.value = data;
    } catch (e) {
        // ignore, next poll will resync
    }
}

function timeSince(isoDate) {
    const minutes = Math.max(0, Math.floor((Date.now() - new Date(isoDate).getTime()) / 60000));
    return `${minutes} min`;
}
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
            v-for="ticket in tickets"
            :key="ticket.id"
            class="border rounded-lg p-4 shadow-sm bg-white"
            :class="ticket.status === 'preparando' ? 'border-blue-300' : 'border-gray-300'"
        >
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-gray-800">Mesa {{ ticket.table_number }}</span>
                <span class="text-xs text-gray-400">{{ timeSince(ticket.created_at) }}</span>
            </div>
            <p class="text-lg">{{ ticket.quantity }}x {{ ticket.product_name }}</p>
            <p v-if="ticket.notes" class="text-sm text-gray-500 italic">{{ ticket.notes }}</p>
            <button
                @click="advance(ticket)"
                class="mt-3 w-full py-1.5 rounded-md text-sm text-white"
                :class="ticket.status === 'pendiente' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700'"
            >
                {{ ticket.status === 'pendiente' ? 'Empezar a preparar' : 'Marcar listo' }}
            </button>
        </div>
        <p v-if="tickets.length === 0" class="text-gray-400 col-span-full text-center py-12">No hay pedidos pendientes.</p>
    </div>
</template>
