export default () => ({
    preview: null,

    selectImage(event) {
        this.destroy();
        const file = event.target.files[0];
        this.preview = file && ['image/jpeg', 'image/png', 'image/webp'].includes(file.type)
            ? URL.createObjectURL(file)
            : null;
    },

    destroy() {
        if (this.preview) {
            URL.revokeObjectURL(this.preview);
        }
    },
});
