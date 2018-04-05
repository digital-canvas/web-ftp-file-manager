<template>
    <div class="mt-4">
        <file-upload
            url='/browser/file'
            :headers="headers"
            :additional-data="params"
            accept="*"
            :max-size="max"
            @error="uploadError"
            @success="fileUploaded"
            @change="selected"
        ></file-upload>
    </div>
</template>

<script>
    import FileUpload from 'v-file-upload';

    export default {
        name: "uploader",
        components: {
            FileUpload
        },
        props: {
            max: {
                type: Number,
                default: 15728640
            },
            directory: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                headers: {},
                params: {
                    directory: this.directory
                }
            }
        },
        methods: {
            selected(){
                this.$emit('changed');
            },
            fileUploaded(e){
                this.$emit('uploaded', e);
            },
            uploadError(e){
                if(e.message){
                    this.$emit('error', e.message);
                } else if (e.target.responseType === 'json' && e.target.response) {
                    this.$emit('error', JSON.parse(e.target.response).message);
                } else {
                    this.$emit('error', 'Failed to upload file');
                }
            }
        },
        mounted() {
            let token = document.head.querySelector('meta[name="csrf-token"]');
            if (token) {
                this.headers = {"X-CSRF-TOKEN": token.content}
            }
        }
    }
</script>
