<template>
    <div>
        <div v-if="!loaded">

            <div class="bg-teal-200 border-t-4 border-teal-500 rounded-b text-teal-700 px-4 py-3 shadow-md"
                 role="alert">
                <div class="flex items-center">
                    <div class="py-1 mr-4"><i class="fas fa-spinner fa-pulse"></i></div>
                    <p>
                        Loading files...
                    </p>
                </div>
            </div>

        </div>
        <div v-else>
            <div>
                <strong>Current Directory</strong> <span v-text="directory"></span>

                <button @click="loadFiles" title="Reload Files" class="bg-transparent text-black mx-2">
                    <i class="fas fa-sync"></i>
                </button>

                <button v-if="directory != '/'" class="px-4 py-2 text-white bg-blue-500 hover:bg-blue-600" @click="up()"
                        title="Move up a directory">
                    <i class="fas fa-arrow-up"></i>
                    Up
                </button>
                <button class="px-4 py-2 text-white bg-green-500 hover:bg-green-600" title="Create a Directory"
                        @click="showCreate">
                    <i class="fas fa-folder"></i>
                    Create Directory
                </button>
                <button class="px-4 py-2 text-white bg-green-500 hover:bg-green-600" title="Upload a file"
                        @click="showUpload">
                    <i class="fas fa-upload"></i>
                    Upload
                </button>
            </div>

            <alert class="my-4" v-if="alert.message" :message="alert.message" :visible="alert.show"
                   :type="alert.type"></alert>

            <v-dialog></v-dialog>


            <div v-if="create.show">
                <div class="my-4 flex">
                    <input class="shadow appearance-none border flex-1 py-2 px-3 text-gray-600" type="text"
                           v-model="create.directory" placeholder="Directory Name">

                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 ml-2" @click="mkdir"
                            :disabled="!validDir">Create
                    </button>
                    <button class="bg-gray-400 hover:bg-gray-500 text-black px-4 py-2 ml-2" @click="hideCreate">Cancel
                    </button>
                </div>
            </div>

            <div v-if="upload.show">
                <uploader
                    :directory="directory"
                    :max="maxSize"
                    @changed="hideAlert"
                    @uploaded="fileUploaded"
                    @error="showAlert"
                ></uploader>

                <div class="mt-2">
                    <button class="bg-gray-400 hover:bg-gray-500 text-black px-4 py-2" @click="hideUpload">Cancel</button>
                </div>
            </div>

            <table class="table-striped">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>File Size</th>
                    <th>Last Modified</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="file in files">
                    <td>
                        <a class="text-blue-500 hover:text-purple-600" href="#" v-if="file.type == 'dir'"
                           title="Change directory" @click="chdir(file.name)">
                            <i class="fas fa-folder"></i>
                            {{ file.name }}
                        </a>
                        <span v-if="file.type == 'file'" v-text="file.name"></span>
                    </td>
                    <td v-text="file.fsize"></td>
                    <td v-text="file.last_modified"></td>
                    <td>
                        <a :href="download(file)" v-if="file.type == 'file'" title="Download"
                           class="bg-transparent text-black mx-2 no-underline">
                            <i class="fas fa-download"></i>
                        </a>
                        <button @click="showDelete(file)" v-if="['dir', 'file'].includes(file.type)"
                                class="bg-transparent fas-fa-trash-alt text-red-600 mx-2" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="text-red-600 bg-red-400 p-4" v-if="files.length == 0">Directory is empty</div>
        </div>


    </div>
</template>

<script>
    export default {
        name: "file-browser",
        props: {
            dataDirectory: {
                type: String,
                default: '/'
            },
            maxSize: {
                type: Number,
                default: 15728640
            },
            message: {
                type: String,
                required: false
            }
        },
        data() {
            return {
                loaded: false,
                directory: this.dataDirectory,
                files: [],
                alert: {
                    show: false,
                    type: 'error',
                    message: ''
                },
                upload: {
                    show: false
                },
                create: {
                    show: false,
                    directory: ''
                }
            }
        },
        computed: {
            validDir() {
                if (this.create.directory == '') {
                    return false;
                }
                return true;
            }
        },
        methods: {
            loadFiles() {
                this.loaded = false;
                axios.get('/browser', {
                    params: {
                        directory: this.directory
                    }
                }).then(response => {
                    this.directory = response.data.directory;
                    this.files = response.data.files;
                    this.loaded = true;
                });
            },
            up() {
                this.hideAlert();
                this.hideUpload();
                this.hideCreate();
                this.loaded = false;
                axios.post('/browser/up', {current: this.directory}).then(response => {
                    if (response.data.success) {
                        this.directory = response.data.directory;
                        this.files = response.data.files;
                    } else if (response.data.message) {
                        this.showAlert(response.data.message);
                    }

                    this.loaded = true;
                });
            },
            chdir(directory) {
                this.hideAlert();
                this.hideUpload();
                this.hideCreate();
                this.loaded = false;
                axios.post('/browser/dir', {
                    current: this.directory,
                    directory: directory
                }).then(response => {
                    if (response.data.success) {
                        this.directory = response.data.directory;
                        this.files = response.data.files;
                    } else if (response.data.message) {
                        this.showAlert(response.data.message)
                    }

                    this.loaded = true;
                });
            },
            mkdir() {
                this.loaded = false;
                this.hideAlert();
                axios.post('/browser/mkdir', {
                    current: this.directory,
                    directory: this.create.directory
                }).then(response => {
                    if (response.data.success) {
                        this.hideCreate();
                        this.directory = response.data.directory;
                        this.files = response.data.files;
                    } else if (response.data.message) {
                        this.showAlert(response.data.message)
                    }

                    this.loaded = true;
                });
            },
            showDelete(file) {
                if (file.type == 'file') {
                    this.$modal.show('dialog', {
                        title: 'Delete File',
                        text: 'Are you sure you want to delete this file?<br>This action cannot be undone.',
                        buttons: [
                            {
                                title: 'Delete File',
                                default: true,
                                handler: () => {
                                    this.deleteFile(file);
                                }
                            },
                            {
                                title: 'Close'
                            }
                        ]
                    });
                } else {
                    this.$modal.show('dialog', {
                        title: 'Delete Directory',
                        text: 'Are you sure you want to delete this directory?<br>This action cannot be undone.',
                        buttons: [
                            {
                                title: 'Delete Directory',
                                default: true,
                                handler: () => {
                                    this.deleteDirectory(file);
                                }
                            },
                            {
                                title: 'Close'
                            }
                        ]
                    });
                }
            },
            deleteFile(file) {
                this.loaded = false;
                this.hideAlert();
                this.hideUpload();
                this.hideCreate();
                axios.delete('/browser/file', {
                    data: {
                        directory: this.directory,
                        file: file.name
                    }
                }).then(response => {
                    if (response.data.success) {
                        this.directory = response.data.directory;
                        this.files = response.data.files;
                        this.showAlert('File has been deleted.', 'success');
                    } else if (response.data.message) {
                        this.showAlert(response.data.message)
                    }

                    this.loaded = true;
                });
            },
            deleteDirectory(directory) {
                this.loaded = false;
                this.hideAlert();
                this.hideUpload();
                this.hideCreate();
                axios.delete('/browser/dir', {
                    data: {
                        current: this.directory,
                        directory: directory.name
                    }
                }).then(response => {
                    if (response.data.success) {
                        this.directory = response.data.directory;
                        this.files = response.data.files;
                        this.showAlert('Directory has been deleted.', 'success');
                    } else if (response.data.message) {
                        this.showAlert(response.data.message);
                    }

                    this.loaded = true;
                });
            },
            fileUploaded(e) {
                if (e.target.response.success) {
                    this.hideUpload();
                    this.directory = e.target.response.directory;
                    this.files = e.target.response.files;
                    this.showAlert('File has been uploaded', 'success');
                } else if (e.target.response.message) {
                    this.showAlert(e.target.response.message);
                }
            },
            download(file) {
                let directory = '/' + this.directory.replace(/^\/*/, "").replace(/\/*$/, "");
                if (directory === '/') {
                    directory = '';
                }
                return '/browser/file' + directory + '/' + file.name;
            },

            showAlert(message, type = 'error') {
                this.alert.message = message;
                this.alert.type = type || 'error';
                this.alert.show = true;
            },
            hideAlert() {
                this.alert.show = false;
                this.alert.message = '';
                this.alert.type = 'error';
            },

            showUpload() {
                this.hideCreate();
                this.upload.show = true;
            },
            hideUpload() {
                this.upload.show = false;
            },
            showCreate() {
                this.hideUpload();
                this.create.directory = '';
                this.create.show = true;
            },
            hideCreate() {
                this.create.show = false;
                this.create.directory = '';
            }
        },
        mounted() {
            this.loadFiles();
            if (this.message) {
                this.showAlert(this.message);
            }
        }
    }
</script>
