export default const download (props) {
  const { content, type, name } = props;

  const file = new Blob(['\ufeff', content], { type });

  const link = document.createElement('a');

  link.id = `_export_datatable_${name}`;
  link.download = name;
  link.href = window.URL.createObjectURL(file);

  document.body.appendChild(link);

  link.click();

  document.getElementById(link.id).remove();
};
