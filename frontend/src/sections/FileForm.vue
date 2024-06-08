<template>
  <div class="flex justify-content-center align-content-center flex-wrap">
    <Toast />
    <div class="flex align-items-center justify-content-center mx-2">
      <div class="flex flex-column gap-2">
        <label for="filename">Filename</label>
        <InputText id="filename" v-model="filename" aria-describedby="filename-help" />
      </div>
    </div>
    <div class="flex align-items-center justify-content-center mx-2">
      <div class="flex flex-column gap-2">
        <label for="description">File Description</label>
        <Textarea id="description" v-model="description" rows="1" cols="30" />
      </div>
    </div>
    <div class="flex align-items-center justify-content-center mx-2 mt-4">
      <FileUpload mode="basic" name="file" accept=".csv" :maxFileSize="1000000000" @select="onFileSelect"
        class="custom-file-upload" />
    </div>
    <div class="flex align-items-center justify-content-center mx-2 mt-4">
      <Button label="Submit" icon="pi pi-check" style="padding: 0.7rem 1rem !important;" :disabled="loading"
        @click="handleSubmit" />
    </div>
  </div>
</template>

<script setup>
import { ref, getCurrentInstance } from 'vue';
import { useToast } from 'primevue/usetoast';
import { store } from '@/api/importedFiles.js'; // Adjust the import path as needed
import FileUpload from 'primevue/fileupload';
import JSZip from 'jszip';
import Toast from 'primevue/toast';

const toast = useToast();
const { emit } = getCurrentInstance(); // Accessing emit function

const filename = ref('');
const description = ref('');
const selectedFile = ref(null);
const loading = ref(false);

const onFileSelect = (event) => {
  selectedFile.value = event.files[0];
};

const handleSubmit = async () => {
  if (!selectedFile.value) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No file selected', life: 3000 });
    console.error('No file selected');
    return;
  }

  loading.value = true;

  const zip = new JSZip();
  zip.file(selectedFile.value.name, selectedFile.value);

  try {
    const zippedContent = await zip.generateAsync({
      type: 'blob',
      compression: 'DEFLATE',
      compressionOptions: {
        level: 9  // Highest compression level
      }
    });

    const zippedFile = new File([zippedContent], `${selectedFile.value.name}.zip`, { type: 'application/zip' });

    const formData = new FormData();
    formData.append('name', filename.value);
    formData.append('description', description.value);
    formData.append('csv_file', zippedFile);

    const response = await store(formData);
    console.log('Data stored successfully:', response);
    emit('uploadSuccess');
    filename.value = '';
    description.value = '';
    selectedFile.value = null;
    loading.value = false;
    toast.add({ severity: 'success', summary: 'Success', detail: 'Data stored successfully', life: 3000 });
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Error storing data: ' + error, life: 3000 });
    console.error('Error storing data:', error);
  } finally {
    loading.value = false;
  }
};
</script>

<style>
.p-button {
  gap: 4px;
}
</style>
