export const getManuscriptLink = (ms) => {
  let url;
  let isExternal = false;
  let hasLink = true;

  if (ms.url) {
    url = ms.url;
    isExternal = true;
  } else if (ms.id) {
    const arkId = ms.id.split('/').pop();
    url = `/manuscripts/${arkId}`;
  } else {
    url = '#';
    hasLink = false;
  }

  return { hasLink, url, isExternal };
};
