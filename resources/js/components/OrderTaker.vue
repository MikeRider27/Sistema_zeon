<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    table: { type: Object, required: true },
    initialOrder: { type: Object, default: null },
    categories: { type: Array, required: true },
    dataUrl: { type: String, required: true },
    sendUrl: { type: String, required: true },
    deleteItemUrlBase: { type: String, required: true },
    deliverUrlBase: { type: String, required: true },
});

const order = ref(props.initialOrder);
const cart = ref([]);
const activeCategory = ref(props.categories[0]?.id ?? null);
const sending = ref(false);
const errorMessage = ref('');

let timer = null;

async function refresh() {
    try {
        const { data } = await axios.get(props.dataUrl);
        order.value = data;
    } catch (e) {
        // ignore transient polling errors
    }
}

onMounted(() => {
    timer = setInterval(refresh, 5000);
});

onUnmounted(() => clearInterval(timer));

function addToCart(product) {
    const existing = cart.value.find((line) => line.product_id === product.id);
    if (existing) {
        existing.quantity++;
    } else {
        cart.value.push({ product_id: product.id, name: product.name, price: Number(product.price), quantity: 1, notes: '' });
    }
}

function decrement(line) {
    line.quantity--;
    if (line.quantity <= 0) {
        cart.value = cart.value.filter((l) => l !== line);
    }
}

const cartTotal = computed(() =>
    cart.value.reduce((sum, line) => sum + line.price * line.quantity, 0)
);

const activeProducts = computed(() => {
    const category = props.categories.find((c) => c.id === activeCategory.value);
    return category ? category.products : [];
});

async function sendToKitchen() {
    if (cart.value.length === 0) return;

    sending.value = true;
    errorMessage.value = '';

    try {
        const { data } = await axios.post(props.sendUrl, {
            items: cart.value.map((line) => ({
                product_id: line.product_id,
                quantity: line.quantity,
                notes: line.notes || null,
            })),
        });
        order.value = data;
        cart.value = [];
    } catch (e) {
        errorMessage.value = 'No se pudo enviar el pedido a cocina.';
    } finally {
        sending.value = false;
    }
}

async function removeItem(item) {
    try {
        const { data } = await axios.delete(`${props.deleteItemUrlBase}/${item.id}`);
        order.value = data;
    } catch (e) {
        errorMessage.value = 'No se pudo quitar ese producto.';
    }
}

async function markDelivered(item) {
    try {
        const { data } = await axios.patch(`${props.deliverUrlBase}/${item.id}/entregado`);
        order.value = data;
    } catch (e) {
        errorMessage.value = 'No se pudo marcar como entregado.';
    }
}

const statusLabels = {
    pendiente: 'Pendiente',
    preparando: 'Preparando',
    listo: 'Listo',
    entregado: 'Entregado',
};

const statusClasses = {
    pendiente: 'bg-gray-100 text-gray-700',
    preparando: 'bg-blue-100 text-blue-700',
    listo: 'bg-green-100 text-green-700',
    entregado: 'bg-gray-100 text-gray-500',
};
</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="flex gap-2 overflow-x-auto pb-2 mb-4">
                <button
                    v-for="category in categories"
                    :key="category.id"
                    @click="activeCategory = category.id"
                    class="px-3 py-1.5 rounded-full text-sm whitespace-nowrap border"
                    :class="activeCategory === category.id ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-600 border-gray-300'"
                >
                    {{ category.name }}
                </button>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <button
                    v-for="product in activeProducts"
                    :key="product.id"
                    @click="addToCart(product)"
                    class="text-left border rounded-lg overflow-hidden bg-white hover:shadow-md transition"
                >
                    <img v-if="product.image_url" :src="product.image_url" class="w-full h-24 object-cover">
                    <div class="p-2">
                        <p class="text-sm font-medium text-gray-800 leading-tight">{{ product.name }}</p>
                        <p class="text-sm text-gray-500">${{ Number(product.price).toFixed(2) }}</p>
                    </div>
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 h-fit">
            <h3 class="font-semibold text-gray-800 mb-3">Nuevo pedido</h3>
            <div v-if="cart.length === 0" class="text-sm text-gray-400">Toca un producto para agregarlo.</div>
            <ul class="space-y-2 mb-4">
                <li v-for="line in cart" :key="line.product_id" class="flex items-center justify-between text-sm">
                    <span>{{ line.name }}</span>
                    <span class="flex items-center gap-2">
                        <button @click="decrement(line)" class="w-6 h-6 rounded-full bg-gray-100 hover:bg-gray-200">-</button>
                        {{ line.quantity }}
                        <button @click="line.quantity++" class="w-6 h-6 rounded-full bg-gray-100 hover:bg-gray-200">+</button>
                    </span>
                </li>
            </ul>
            <div v-if="cart.length" class="flex items-center justify-between font-medium text-sm mb-3">
                <span>Subtotal</span>
                <span>${{ cartTotal.toFixed(2) }}</span>
            </div>
            <button
                @click="sendToKitchen"
                :disabled="cart.length === 0 || sending"
                class="w-full py-2 bg-gray-800 text-white rounded-md text-sm disabled:opacity-40"
            >
                {{ sending ? 'Enviando…' : 'Enviar a cocina' }}
            </button>
            <p v-if="errorMessage" class="text-sm text-red-600 mt-2">{{ errorMessage }}</p>

            <div v-if="order && order.items.length" class="mt-6 border-t pt-4">
                <h4 class="font-semibold text-gray-800 mb-2">Pedido de la mesa {{ table.number }}</h4>
                <ul class="space-y-2">
                    <li v-for="item in order.items" :key="item.id" class="text-sm">
                        <div class="flex items-center justify-between">
                            <span>{{ item.quantity }}x {{ item.product_name }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs" :class="statusClasses[item.status]">
                                {{ statusLabels[item.status] }}
                            </span>
                        </div>
                        <div class="flex gap-3 mt-1">
                            <button v-if="item.status === 'pendiente'" @click="removeItem(item)" class="text-xs text-red-600 hover:underline">Quitar</button>
                            <button v-if="item.status === 'listo'" @click="markDelivered(item)" class="text-xs text-green-700 hover:underline">Marcar entregado</button>
                        </div>
                    </li>
                </ul>
                <div class="flex items-center justify-between font-semibold mt-3 pt-3 border-t">
                    <span>Total</span>
                    <span>${{ order.total.toFixed(2) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
