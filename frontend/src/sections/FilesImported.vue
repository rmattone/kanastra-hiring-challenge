<template>
    <div class="flex justify-content-center align-content-center">
        <DataTable :value="items" tableStyle="min-width: 52rem" stripedRows paginator :rows="5"
            :rowsPerPageOptions="[5, 10, 20, 50]">
            <!-- <Column field="id" header="Code"></Column> -->
            <Column field="name" header="Name"></Column>
            <Column field="description" header="Description"></Column>
            <Column field="path" header="Path"></Column>
            <!-- <Column field="status" header="Status"></Column> -->
            <Column header="Status">
                <template #body="slotProps">
                    <Tag :value="getValue(slotProps.data.status)" :severity="getSeverity(slotProps.data.status)" />
                </template>
            </Column>
            <Column field="created_at" header="Created At"></Column>
            <template #footer> In total there are {{ items ? items.length : 0 }} items. </template>
        </DataTable>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { list } from '@/api/importedFiles.js';

const items = ref([]);
let interval = null;

const getSeverity = (status) => {
    if (status == 1) {
        return 'secondary';
    }
    if (status == 2) {
        return 'primary';
    }
    if (status == 3) {
        return 'error';
    }
    if (status == 4) {
        return 'success';
    }
}

const getValue = (status) => {
    if (status == 1) {
        return 'Pending';
    }
    if (status == 2) {
        return 'Processing';
    }
    if (status == 3) {
        return 'Error';
    }
    if (status == 4) {
        return 'Completed';
    }
}

const loadItems = () => {
    list().then((response) => {
        console.log(response);
        const response_items = []
        response.forEach(element => {
            response_items.push({
                id: element.id,
                name: element.name,
                description: element.description,
                path: element.path,
                status: element.status,
                created_at: element.created_at
            });
        });

        items.value = response_items;

        // Verifica se há itens com status "Pending" ou "Processing"
        const hasPendingOrProcessing = response_items.some(item => item.status === 1 || item.status === 2);

        // Configura ou limpa o intervalo conforme necessário
        if (hasPendingOrProcessing && !interval) {
            interval = setInterval(loadItems, 10000);
        } else if (!hasPendingOrProcessing && interval) {
            clearInterval(interval);
            interval = null;
        }
    });
}

// Load items when the component is mounted
onMounted(() => {
    loadItems();
});

// Clear the interval when the component is unmounted
onBeforeUnmount(() => {
    if (interval) {
        clearInterval(interval);
    }
});
</script>

<style>
.p-datatable-footer {
    text-align: end;
}
</style>
