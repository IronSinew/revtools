import vue from "eslint-plugin-vue";
import simpleImportSort from "eslint-plugin-simple-import-sort";
import prettier from "eslint-plugin-prettier";
import globals from "globals";
import path from "node:path";
import { fileURLToPath } from "node:url";
import js from "@eslint/js";
import { FlatCompat } from "@eslint/eslintrc";
import jsdoc from "eslint-plugin-jsdoc";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const compat = new FlatCompat({
    baseDirectory: __dirname,
    recommendedConfig: js.configs.recommended,
    allConfig: js.configs.all,
});

export default [
    ...compat.extends(
        "eslint:recommended",
        "plugin:vue/recommended",
        "prettier",
    ),
    {
        plugins: {
            vue,
            "simple-import-sort": simpleImportSort,
            prettier,
            jsdoc: jsdoc,
        },

        languageOptions: {
            globals: {
                ...globals.browser,
                visit: "readonly",
                route: "readonly",
                filters: "readonly",
                require: true,
                _: true,
                IS_PROD: true,
                APR_FACTOR: true,
                Echo: true,
                axios: true,
                asset: true,
                dateToLocalString: true,
                getFeatureFlag: true,
                jsonObjectToSelectOptions: true,
                tolerant: true,
            },

            ecmaVersion: "latest",
            sourceType: "module",
        },

        rules: {
            "jsdoc/require-description": "warn",
            "jsdoc/check-values": "warn",

            "sort-imports": [
                "error",
                {
                    ignoreDeclarationSort: true,
                    ignoreCase: true,
                },
            ],

            "simple-import-sort/imports": "error",
            "vue/no-unused-components": "warn",

            "no-unused-vars": [
                "warn",
                {
                    varsIgnorePattern: "^_",
                    argsIgnorePattern: "visit",
                },
            ],

            "vue/no-v-html": "off",
            "vue/no-reserved-component-names": "off",
            "vue/name-property-casing": "off",
            "vue/multi-word-component-names": "off",
            "vue/attributes-order": "error",
            "vue/html-quotes": "error",
            "vue/mustache-interpolation-spacing": ["error", "always"],
            "vue/no-side-effects-in-computed-properties": "error",

            "vue/order-in-components": [
                "error",
                {
                    order: [
                        "el",
                        "name",
                        "parent",
                        "LIFECYCLE_HOOKS",
                        "methods",
                        ["template", "render"],
                        "renderError",
                    ],
                },
            ],

            "vue/prop-name-casing": [0, "snake_case"],
            "vue/require-default-prop": "off",
            "vue/require-prop-types": "warn",
            "vue/require-valid-default-prop": "error",
            "vue/return-in-computed-property": "error",

            "prettier/prettier": [
                "error",
                {
                    bracketSameLine: false,
                },
            ],
        },
    },
];
