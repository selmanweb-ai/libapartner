import 'package:flutter/material.dart';
import '../../core/ui/glass_container.dart';

class OrdersScreen extends StatelessWidget {
  const OrdersScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Siparişler', style: TextStyle(fontWeight: FontWeight.bold)),
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: 10,
        itemBuilder: (context, index) {
          final isPending = index % 3 == 0;
          return Card(
            margin: const EdgeInsets.only(bottom: 16),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            child: ListTile(
              contentPadding: const EdgeInsets.all(16),
              leading: Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: isPending ? Colors.orange.withOpacity(0.1) : Colors.green.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  isPending ? Icons.pending_actions : Icons.check_circle_outline,
                  color: isPending ? Colors.orange : Colors.green,
                ),
              ),
              title: Text('Sipariş #${1040 + index}', style: const TextStyle(fontWeight: FontWeight.bold)),
              subtitle: const Text('Ahmet Yılmaz\n₺350.00'),
              isThreeLine: true,
              trailing: Chip(
                label: Text(isPending ? 'Bekliyor' : 'Tamamlandı'),
                backgroundColor: isPending ? Colors.orange.withOpacity(0.1) : Colors.green.withOpacity(0.1),
                labelStyle: TextStyle(
                  color: isPending ? Colors.orange : Colors.green,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                ),
              ),
              onTap: () {
                // TODO: Sipariş Detayı
              },
            ),
          );
        },
      ),
    );
  }
}
