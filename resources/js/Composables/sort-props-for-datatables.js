import {reactive} from "vue";

export default function dataTablesSortFromProps(arrayProps) {
    const resultValue = [];
    const keys = _.filter(_.map(arrayProps, 'field'));
    const values = _.filter(_.map(arrayProps, 'order'));

    _.each(_.zipObject(keys, values), (value, key) => {
        resultValue.push(reactive({
            field: key,
            order: parseInt(value),
        }));
    });

    return resultValue;
}
