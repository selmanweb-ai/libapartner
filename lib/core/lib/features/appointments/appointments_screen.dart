import 'package:flutter/material.dart';

class AppointmentsScreen extends StatelessWidget {
  const AppointmentsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Randevular / İstekler', style: TextStyle(fontWeight: FontWeight.bold)),
      ),
      body: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: 5,
        separatorBuilder: (context, index) => const Divider(),
        itemBuilder: (context, index) {
          return ListTile(
            leading: const CircleAvatar(
              backgroundColor: Colors.blue,
              child: Icon(Icons.person, color: Colors.white),
            ),
            title: Text('Müşteri ${index + 1}', style: const TextStyle(fontWeight: FontWeight.bold)),
            subtitle: Text('Tarih: 12 Ekim 2026 - 1${index + 2}:00\n"Hizmetler hakkında bilgi almak istiyorum."'),
            isThreeLine: true,
            trailing: IconButton(
              icon: const Icon(Icons.check_circle_outline, color: Colors.green),
              onPressed: () {
                // TODO: Mark as resolved
              },
            ),
          );
        },
      ),
    );
  }
}
