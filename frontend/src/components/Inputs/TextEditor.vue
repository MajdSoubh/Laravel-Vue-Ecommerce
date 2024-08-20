<template>
    <div class="flex gap-2 flex-col">
        <label :for="id">{{ label }}</label>
        <div>
            <ckeditor
                :editor="ClassicEditor"
                v-model="model"
                :config="editorConfig"
            ></ckeditor>
            <div
                v-if="errors"
                class="flex flex-col items-start justify-center mt-1"
            >
                <div v-for="(error, ind) in errors" :key="ind">
                    <span class="text-red-500 text-sm block">
                        * {{ error }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { defineModel, ref } from "vue";
import {
    ClassicEditor,
    Autoformat,
    BlockQuote,
    Bold,
    Essentials,
    Heading,
    Image,
    ImageCaption,
    ImageResize,
    ImageStyle,
    ImageToolbar,
    ImageUpload,
    Base64UploadAdapter,
    Indent,
    IndentBlock,
    Italic,
    Link,
    List,
    Paragraph,
    TextTransformation,
    Underline,
} from "ckeditor5";
import CKEditor from "@ckeditor/ckeditor5-vue";
import "ckeditor5/ckeditor5.css";

const ckeditor = CKEditor.component;

const model = defineModel();
const { label, errors, placeholder, id } = defineProps([
    "label",
    "errors",
    "placeholder",
    "id",
]);
// const classes = ref(
//     "block w-full px-3 py-20 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
// );
const editorConfig = {
    plugins: [
        Autoformat,
        BlockQuote,
        Bold,
        Essentials,
        Heading,
        Image,
        ImageCaption,
        ImageResize,
        ImageStyle,
        ImageToolbar,
        ImageUpload,
        Base64UploadAdapter,
        Indent,
        IndentBlock,
        Italic,
        Link,
        List,
        Paragraph,
        TextTransformation,
        Underline,
    ],
    toolbar: [
        "undo",
        "redo",
        "|",
        "heading",
        "|",
        "bold",
        "italic",
        "underline",
        "|",
        "link",
        "uploadImage",
        "blockQuote",
        "|",
        "bulletedList",
        "numberedList",
        "|",
        "outdent",
        "indent",
    ],
    heading: {
        options: [
            {
                model: "paragraph",
                title: "Paragraph",
                class: "ck-heading_paragraph",
            },
            {
                model: "heading1",
                view: "h1",
                title: "Heading 1",
                class: "ck-heading_heading1",
            },
            {
                model: "heading2",
                view: "h2",
                title: "Heading 2",
                class: "ck-heading_heading2",
            },
            {
                model: "heading3",
                view: "h3",
                title: "Heading 3",
                class: "ck-heading_heading3",
            },
            {
                model: "heading4",
                view: "h4",
                title: "Heading 4",
                class: "ck-heading_heading4",
            },
        ],
    },
    image: {
        resizeOptions: [
            {
                name: "resizeImage:original",
                label: "Default image width",
                value: null,
            },
            {
                name: "resizeImage:50",
                label: "50% page width",
                value: "50",
            },
            {
                name: "resizeImage:75",
                label: "75% page width",
                value: "75",
            },
        ],
        toolbar: [
            "imageTextAlternative",
            "toggleImageCaption",
            "|",
            "imageStyle:inline",
            "imageStyle:wrapText",
            "imageStyle:breakText",
            "|",
            "resizeImage",
        ],
    },
    link: {
        addTargetToExternalLinks: true,
        defaultProtocol: "https://",
    },
};
</script>
