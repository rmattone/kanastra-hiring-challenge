import { expect } from 'chai'; // Importe expect do Chai
import { list } from '../../api/importedFiles.js'; // Ajuste o caminho conforme a estrutura do seu projeto

describe('API Functions', () => {
  describe('list', () => {
    it('should make a GET request to /charges with correct data', async () => {
      // Simule dados de entrada
      const result = list();
      const requestData = await result;
      console.log(requestData);
      expect(requestData).to.be.an('array');
      expect(requestData[0]).to.have.property('id');
    });
  });
});
