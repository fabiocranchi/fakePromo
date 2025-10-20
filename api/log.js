// Vercel Serverless Function para logging
export default async function handler(req, res) {
  // Configurar CORS
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const newEntry = req.body;
    
    if (!newEntry) {
      return res.status(400).json({ error: 'Invalid data' });
    }

    // Aqui você pode:
    // 1. Salvar em banco de dados (MongoDB, PostgreSQL, etc.)
    // 2. Enviar para serviço externo (Airtable, Google Sheets)
    // 3. Enviar por email
    // 4. Usar Vercel KV (Key-Value store)
    
    // Exemplo com console.log (aparece nos logs do Vercel)
    console.log('Nova entrada de log:', JSON.stringify(newEntry, null, 2));
    
    return res.status(200).json({
      success: true,
      message: 'Log entry processed successfully',
      data: newEntry
    });

  } catch (error) {
    console.error('Erro no log:', error);
    return res.status(500).json({ error: 'Internal server error' });
  }
}
