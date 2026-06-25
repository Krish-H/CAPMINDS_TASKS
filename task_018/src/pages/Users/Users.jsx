import { useState } from 'react';
import styled from 'styled-components';
import { TableContainer, StyledTable, TableHead, TableRow, TableHeader, TableCell } from '../../components/Table/Table';
import Button from '../../components/Button/Button';
import Modal from '../../components/Modal/Modal';
import Input from '../../components/Input/Input';
import { Plus, Trash2 } from 'lucide-react';

const PageHeader = styled.div`
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
`;

const Title = styled.h2`
  font-size: 24px;
  font-weight: 700;
  color: ${({ theme }) => theme.colors.text};
`;

const Form = styled.form`
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-top: 20px;
`;

const Users = () => {
  const [users, setUsers] = useState([
    { id: 1, name: 'John Doe', email: 'john@example.com', role: 'Admin' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'User' },
    { id: 3, name: 'Alice Johnson', email: 'alice@example.com', role: 'Editor' },
  ]);

  const [isModalOpen, setIsModalOpen] = useState(false);
  const [newUser, setNewUser] = useState({ name: '', email: '', role: '' });

  const handleDelete = (id) => {
    setUsers(users.filter(user => user.id !== id));
  };

  const handleAddUser = (e) => {
    e.preventDefault();
    if (newUser.name && newUser.email && newUser.role) {
      setUsers([...users, { id: Date.now(), ...newUser }]);
      setIsModalOpen(false);
      setNewUser({ name: '', email: '', role: '' });
    }
  };

  return (
    <div>
      <PageHeader>
        <Title>Users Management</Title>
        <Button icon={Plus} onClick={() => setIsModalOpen(true)}>Add User</Button>
      </PageHeader>

      <TableContainer>
        <StyledTable>
          <TableHead>
            <TableRow>
              <TableHeader>Name</TableHeader>
              <TableHeader>Email</TableHeader>
              <TableHeader>Role</TableHeader>
              <TableHeader>Actions</TableHeader>
            </TableRow>
          </TableHead>
          <tbody>
            {users.map(user => (
              <TableRow key={user.id}>
                <TableCell>{user.name}</TableCell>
                <TableCell>{user.email}</TableCell>
                <TableCell>{user.role}</TableCell>
                <TableCell>
                  <Button 
                    variant="danger" 
                    icon={Trash2} 
                    onClick={() => handleDelete(user.id)}
                  >
                    Delete
                  </Button>
                </TableCell>
              </TableRow>
            ))}
            {users.length === 0 && (
              <TableRow>
                <TableCell colSpan="4" style={{ textAlign: 'center' }}>No users found.</TableCell>
              </TableRow>
            )}
          </tbody>
        </StyledTable>
      </TableContainer>

      <Modal 
        isOpen={isModalOpen} 
        onClose={() => setIsModalOpen(false)}
        title="Add New User"
      >
        <Form onSubmit={handleAddUser}>
          <Input 
            label="Name" 
            placeholder="Enter full name" 
            value={newUser.name}
            onChange={(e) => setNewUser({...newUser, name: e.target.value})}
            required
          />
          <Input 
            label="Email" 
            type="email" 
            placeholder="Enter email address" 
            value={newUser.email}
            onChange={(e) => setNewUser({...newUser, email: e.target.value})}
            required
          />
          <Input 
            label="Role" 
            placeholder="Admin / User / Editor" 
            value={newUser.role}
            onChange={(e) => setNewUser({...newUser, role: e.target.value})}
            required
          />
          <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '10px', marginTop: '10px' }}>
            <Button type="button" variant="secondary" onClick={() => setIsModalOpen(false)}>Cancel</Button>
            <Button type="submit">Save User</Button>
          </div>
        </Form>
      </Modal>
    </div>
  );
};

export default Users;
