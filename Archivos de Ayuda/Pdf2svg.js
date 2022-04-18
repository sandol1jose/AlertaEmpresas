/* Copyright 2014 Mozilla Foundation
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.

Basado en
 https://github.com/mozilla/pdf.js/tree/master/examples/text-only
 */

//const PDF_PATH = "https://www.boe.es/borme/dias/2022/01/24/pdfs/BORME-A-2022-15-03.pdf";
const PDF_PATH = "BORME-A-2022-15-03.pdf";
const PAGE_NUMBER = 1;

pdfjsLib.GlobalWorkerOptions.workerSrc = "pdfjs/build/pdf.worker.js";

pageLoaded();

async function pageLoaded() {
  // Loading document and page text content
  const loadingTask = pdfjsLib.getDocument({ url: PDF_PATH });
  const pdfDocument = await loadingTask.promise;
  const page = await pdfDocument.getPage(PAGE_NUMBER);
  const textContent = await page.getTextContent();
  console.log(textContent);
  //RecorrerArray(textContent);
}

function RecorrerArray(textContent){
    var textItems = textContent.items;
    var finalString = "";

    // Concatenate the string of the item to the final string
    for (var i = 0; i < textItems.length; i++) {
        var item = textItems[i];

        finalString += item.str + " ";
    }

    console.log(finalString);

  
  var textItems = textContent.items;
  for(i=0; i<=147; i++){
    console.log(textContent["items"][i]["str"]);
  }
}